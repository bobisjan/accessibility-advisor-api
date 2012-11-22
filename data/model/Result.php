<?php

namespace AccessibilityAdvisor;

use Nette;



/**
 * Result (uses original SQL queries).
 *
 * @author Jan Bobisud
 */
class Result extends Nette\Object
{

	/** @var Store */
	private $store;
	
	/** @var array */
	private $ids;
	
	/** @var array */
	private $parameters;



	public function __construct(Store $store)
	{
		$this->store = $store;
	}



	public function findAll($parameters)
	{
		$this->parameters = $this->prepareParameters($parameters);
		$result = new Nette\ArrayHash();
		
		$result->recommendations = $this->recommendations();
		$result->personas = $this->personas();
		$result->tools = $this->tools();
//		$result->devices = $this->devices();
//		$result->model = $this->model();
		
		return $result;
	}
	
	
	
	private function arrayToInSql($ids)
	{
		$length = count($ids);
		$i = 0;
		$sql = "IN (";
		
		foreach ($ids as $id) {
			$sql .= "'" . $id . "'";
			$i++;
			
			if ($i !== $length) {
				$sql .= ",";
			}
		}
		$sql .= ")";
		return $sql;
	}
	
	
	
	private function prepareParameters($parameters)
	{
		$params = array();
		
		$params[] = $parameters['application-type'];
		
		if (isset($parameters['disability'])) {
			$params[] = $this->arrayToInSql($parameters['disability']);
		} else {
			$params[] = NULL;
		}
		
		if (isset($parameters['lighting-conditions'])
			|| isset($parameters['environment-noisiness'])) {
			$ps = array();
			
			if (isset($parameters['lighting-conditions'])) {
				$ps = array_merge($ps, $parameters['lighting-conditions']);
			}
			if (isset($parameters['environment-noisiness'])) {
				$ps = array_merge($ps, $parameters['environment-noisiness']);
			}
			$params[] = $this->arrayToInSql($ps);
		} else {
			$params[] = NULL;
		}
		
		if (isset($parameters['age'])) {
			$params[] = $this->arrayToInSql($parameters['age']);
		} else {
			$params[] = NULL;
		}

		return $params;
	}
	
	
	
	private function generealRecommendations()
	{
	    $items = array();
	    
	    $items[] = Nette\ArrayHash::from(array(
	        'title' => "Web Content Accessibility Guidelines (WCAG) 2.0",
	        'url' => "http://www.w3.org/TR/WCAG20/",
	        'description' => "WACAG 2.0 contains wide range of recommendations for making web pages accessible to people with various disabilities. Following those guidelines will also help other people to better experience."));
	    
	    $items[] = Nette\ArrayHash::from(array(
	        'title' => "WAI-ARIA",
	        'url' => "http://www.w3.org/WAI/intro/aria.php",
	        'description' => "WAI - ARIA is a specification of a way of development that should be followed in order to make modern interactive Rich Internet Application accessible."));
	    
	    $items[] = Nette\ArrayHash::from(array(
	        'title' => "ISO 13066",
	        'url' => "http://www.iso.org/iso/catalogue_detail.htm?csnumber=53770",
	        'description' => "International standard that specifies Interoperability of information technologies (IT) with Assistive Technology (AT). It identifies a variety of common accessibility APIs that are described further in other parts of ISO/IEC 13066."));

        $items[] = Nette\ArrayHash::from(array(
            'title' => "EU Policy",
            'url' => "http://ec.europa.eu/ipg/standards/accessibility/eu_policy/index_en.htm",
            'description' => "European Union guidelines for accessibility."));
        
        $items[] = Nette\ArrayHash::from(array(
            'title' => "Section 508",
            'url' => "http://www.section508.gov/",
            'description' => "Section 508 is US organization that deals with barriers in information technologies on Federal level."));
	    
	    return $items;
	}
	
	
	
	private function recommendations()
	{
		$database = $this->store->database;
		list($applicationTypeId, $disabilityIds, $contextOfUseIds, $ageIds) = $this->parameters;
		
		$rows = $database->fetchAll(
			$this->getRecommendationsSql($applicationTypeId, $contextOfUseIds, $ageIds, $disabilityIds));

		$this->ids = array();
		$recommendations = array();
		
		$sql = "SELECT * FROM Recommendation WHERE idRecommendation = ?;";
		
		foreach ($rows as $row) {
			$id = $row['idRecommendation'];
			$this->ids[] = $id;
			
			foreach ($database->fetchAll($sql, $id) as $value) {				
				$recommendations[] = $this->createRecomendation($value,
					$this->parameters($this->getImpairmentSql($id), 'title'),
					$this->parameters($this->getDisabilitySql($id, $disabilityIds), 'ICFCodeQualifiedTitle'),
					$this->parameters($this->getAgeSql($id, $ageIds), 'groupName'),
					$this->parameters($this->getNoiseSql($id, $contextOfUseIds), 'title'),
					$this->parameters($this->getLightSql($id, $contextOfUseIds), 'title'));
			}
		}
		
		$recommendations = array_merge($recommendations, $this->generealRecommendations())
		
		return $recommendations;
	}
	
	
	
	private function createRecomendation($row, $impairments, $disabilities, $ages, $noises, $lights)
	{
		$recomendation = new Nette\ArrayHash();
		
		$recomendation->title = $row->title;
		$recomendation->description = $row->description;
		$recomendation->stressDescription = ($row->stressDescription) ?: NULL;
		$recomendation->developerToolFamily = ($row->developerToolFamily) ?: NULL;
		
		$recomendation->impairments = $impairments;
		$recomendation->disabilities = $disabilities;
		$recomendation->ages = $ages;
		$recomendation->noises = $noises;
		$recomendation->lights = $lights;
		
		return $recomendation;
	}
	
	
	
	private function parameters($sql, $column)
	{
		$database = $this->store->database;
		$values = array();
		
		foreach ($database->fetchAll($sql) as $item) {
			$values[] = $item[$column];
		}
		return $values;
	}
	
	
	
	private function personas()
	{
		$database = $this->store->database;
		$personas = array();
		
		list($applicationTypeId, $disabilityIds, $contextOfUseIds, $ageIds) = $this->parameters;
		
		foreach($database->fetchAll($this->sqlForPersonas($disabilityIds)) as $row) {
			$personas[] = $this->createPersona($row, true);
		}
		foreach($database->fetchAll($this->sqlForPersonas($disabilityIds, FALSE)) as $row) {
			$personas[] = $this->createPersona($row, false);
		}
		return $personas;
	}
	
	
	
	private function createPersona($row, $primary)
	{
		$persona = new Nette\ArrayHash();
		
		$persona->isPrimary = $primary;
		
		$persona->fullName = $row->name;
		$persona->photo = $row->pathToPhoto;
		$persona->age = $row->age;
		$persona->meritalStatus = $row->meritalStatus;
		$persona->living = $row->living;
		$persona->children = $row->children;
		$persona->location = $row->location;
		$persona->education = $row->education;
		$persona->job = $row->impairment;
		
		$persona->format = $row->personaFormat;
		
		$persona->introduction = $row->personaIntroduction;
		$persona->technology = $row->personaTechnology;
		$persona->problems = $row->personaProblems;
		$persona->needs = $row->personaNeeds;
		$persona->month = $row->personaMonth;
	
		$persona->freeText = $row->personaFreeText;
		
		return $persona;
	}
	
	
	
	private function sqlForPersonas($disabilityIds, $primary = TRUE)
	{
		$sql = "SELECT * FROM (SELECT *, COUNT(*) AS cc
	             FROM Persona
	               JOIN Persona_has_Disability ON Persona.`idPersona`=Persona_has_Disability.`Persona_idPersona`
	            	JOIN Disability ON Persona_has_Disability.`Disability_idDisability`=Disability.`idDisability`
	
	               WHERE Disability.`idDisability` ".$disabilityIds."
	               GROUP BY Persona.`name`) AS T ORDER BY cc DESC LIMIT ";

		$sql .= ($primary) ? "1;" : "1,2;";
		return $sql;
	}
	
	
	
	private function tools()
	{
		$database = $this->store->database;
		$ids = $this->arrayToInSql($this->ids);
		$tools = array();
		
		foreach ($database->fetchAll($this->sqlForTools($ids)) as $id) {
			$id = $id['idDeveloper_Tool'];
			$sql = "SELECT * FROM Developer_Tool WHERE idDeveloper_Tool = " . $id . ";";
			
			foreach ($database->fetchAll($sql) as $row) {
				$tools[] = $this->createTool(
					$row,
					$database->fetchAll($this->sqlForDeveloperCategories($id)),
					$database->fetchAll($this->sqlForDeveloperPlatforms($id)),
					$database->fetchAll($this->sqlForUserPlatforms($id)));
			}
		}
		return $tools;
	}
	
	
	
	private function createTool($row, $categories, $developerPlatforms, $userPlatforms)
	{
		$tool = new Nette\ArrayHash();
		
		$tool->title = $row->title;
		$tool->description = $row->description;
		$tool->homePageUrl = $row->homePageLink;
		$tool->documentationUrl = $row->documentationPageLink;
		$tool->tutorialUrl = $row->tutorialPageLink;
		
		$tool->categories = array();
		foreach ($categories as $category) {
			$tool->categories[] = $category['developerToolCathegoryName'];
		}
		
		$tool->developerPlatforms = array();
		foreach ($developerPlatforms as $platform) {
			$tool->developerPlatforms[] = $platform['devOperatingSystem'];
		}
		
		$tool->userPlatforms = array();
		foreach ($userPlatforms as $platform) {
			$tool->userPlatforms[] = $platform['usrOperatingSystem'];
		}
		
		return $tool;
	}
	
	
	
	private function sqlForTools($recommendationIds)
	{
		$sql = "SELECT DISTINCT Developer_Tool.`idDeveloper_Tool` FROM (Recommendation LEFT JOIN Recommendation_has_Developer_Tool ON Recommendation.`idRecommendation`=Recommendation_has_Developer_Tool.`Recommendation_idRecommendation`)
		             LEFT JOIN Developer_Tool ON Recommendation_has_Developer_Tool.`Developer_Tool_idDeveloper_Tool`=Developer_Tool.`idDeveloper_Tool`
		             WHERE Recommendation.`idRecommendation` ".$recommendationIds." AND Developer_Tool.`idDeveloper_Tool` IS NOT NULL ;";
		return $sql;
	}
	
	
	
	private function sqlForDeveloperPlatforms($id)
	{
		return "SELECT Developer_Platform.`devOperatingSystem` FROM (Developer_Tool LEFT JOIN Developer_Tool_has_Developer_Platform ON Developer_Tool.`idDeveloper_Tool`=Developer_Tool_has_Developer_Platform.`Developer_Tool_idDeveloper_Tool`) LEFT JOIN Developer_Platform ON Developer_Tool_has_Developer_Platform.`Developer_Platform_idDeveloper_Platform`=Developer_Platform.`idDeveloper_Platform` WHERE Developer_Tool.`idDeveloper_Tool`=" . $id . ";";
	}
	
	
	
	private function sqlForUserPlatforms($id)
	{
		return "SELECT User_Platform.`usrOperatingSystem` FROM (Developer_Tool LEFT JOIN Developer_Tool_has_User_Platform ON Developer_Tool.`idDeveloper_Tool`= Developer_Tool_has_User_Platform.`Developer_Tool_idDeveloper_Tool`) LEFT JOIN User_Platform ON Developer_Tool_has_User_Platform.`User_Platform_idUser_Platform`=User_Platform.`idUser_Platform`  WHERE Developer_Tool.`idDeveloper_Tool`=" . $id . ";";
	}
	
	
	
	private function sqlForDeveloperCategories($id) {
		return "SELECT Developer_Tool_Cathegory.`developerToolCathegoryName` FROM (Developer_Tool LEFT JOIN Developer_Tool_has_Developer_Tool_Cathegory ON Developer_Tool.`idDeveloper_Tool`= Developer_Tool_has_Developer_Tool_Cathegory.`Developer_Tool_idDeveloper_Tool`) LEFT JOIN Developer_Tool_Cathegory ON Developer_Tool_has_Developer_Tool_Cathegory.`Developer_Tool_Cathegory_idDeveloper_Tool_Cathegory`=Developer_Tool_Cathegory.`idDeveloper_Tool_Cathegory` WHERE Developer_Tool.`idDeveloper_Tool`=" . $id . ";";
	}
	
	
	
	private function devices()
	{
		return array();
	}
	
	
	
	private function model()
	{
		return new Nette\ArrayHash();
	}
	
	
	
	private function getAgeSql($recommendationId, $ageIds)
	{
		return "SELECT * FROM Age_Group LEFT JOIN Recommendation_has_Age_Group ON Age_Group.`idAge_Group`= Recommendation_has_Age_Group.`Age_Group_idAge_Group` WHERE Recommendation_has_Age_Group.`Recommendation_idRecommendation` = ".$recommendationId." AND Age_Group.`idAge_Group` ".$ageIds.";";
	}
	
	
	
	private function getLightSql($recommendationId, $contextOfUseIds)
	{
		return "SELECT * FROM Context_Of_Use LEFT JOIN Recommendation_has_Context_Of_Use ON Context_Of_Use.`idContext_Of_Use`=Recommendation_has_Context_Of_Use.`Context_Of_Use_idContext_Of_Use` WHERE Recommendation_has_Context_Of_Use.`Recommendation_idRecommendation` = ".$recommendationId." AND Context_Of_Use.`idContext_Of_Use` ".$contextOfUseIds." AND Context_Of_Use.`contextCathegory`='lighting';";
	}
	
	
	
	private function getNoiseSql($recommendationId, $contextOfUseIds)
	{
		return "SELECT * FROM Context_Of_Use LEFT JOIN Recommendation_has_Context_Of_Use ON Context_Of_Use.`idContext_Of_Use`=Recommendation_has_Context_Of_Use.`Context_Of_Use_idContext_Of_Use` WHERE Recommendation_has_Context_Of_Use.`Recommendation_idRecommendation` = ".$recommendationId." AND Context_Of_Use.`idContext_Of_Use` ".$contextOfUseIds." AND Context_Of_Use.`contextCathegory`='noisiness';";
	}
	
	
	
	private function getDisabilitySql($recommendationId, $disabilityIds)
	{
		return "SELECT * FROM Disability LEFT JOIN Recommendation_has_Disability ON Disability.`idDisability`= Recommendation_has_Disability.`Disability_idDisability` WHERE Recommendation_has_Disability.`Recommendation_idRecommendation` = ".$recommendationId." AND Disability.`idDisability` ".$disabilityIds."  ;";
	}
	
	
	
	private function getImpairmentSql($recommendationId)
	{
		return "SELECT DISTINCT title FROM (Disability LEFT JOIN Recommendation_has_Disability ON Disability.`idDisability`= Recommendation_has_Disability.`Disability_idDisability`
		   ) LEFT JOIN Impairment ON Disability.`Impairment_idImpairment`= Impairment.`idImpairment`
		  WHERE Recommendation_has_Disability.`Recommendation_idRecommendation` = ".$recommendationId.";";
	}
	
	
	
	private function getRecommendationsSql($applicationTypeId, $contextOfUseIds, $ageIds, $disabilityIds)
	{
		return "
		       SELECT DISTINCT Recommendation.idRecommendation FROM
			  (((((((Recommendation LEFT JOIN Recommendation_has_Application_Type
			   ON Recommendation.`idRecommendation`=Recommendation_has_Application_Type.`Recommendation_idRecommendation`
			   )
			      LEFT JOIN Application_Type
			      ON Recommendation_has_Application_Type.`Application_Type_idApplication_Type`=Application_Type.`idApplication_Type`
			      )
			         LEFT JOIN Recommendation_has_Context_Of_Use
			         ON Recommendation.`idRecommendation`=Recommendation_has_Context_Of_Use.`Recommendation_idRecommendation`
			         )
				    LEFT JOIN Context_Of_Use
				    ON Recommendation_has_Context_Of_Use.`Context_Of_Use_idContext_Of_Use`=Context_Of_Use.`idContext_Of_Use`
				    )
				       LEFT JOIN Recommendation_has_Age_Group
				       ON Recommendation.`idRecommendation`=Recommendation_has_Age_Group.`Recommendation_idRecommendation`
				       )
					  LEFT JOIN Age_Group
					  ON Recommendation_has_Age_Group.`Age_Group_idAge_Group`=Age_Group.`idAge_Group`
					  )
					     LEFT JOIN Recommendation_has_Disability
					     ON Recommendation.`idRecommendation`=Recommendation_has_Disability.`Recommendation_idRecommendation`
					     )
					        LEFT JOIN Disability
					        ON Recommendation_has_Disability.`Disability_idDisability`=Disability.`idDisability`
		
		         WHERE Application_Type.`idApplication_Type` = ".$applicationTypeId." AND ((Recommendation.`idRecommendation` NOT IN (SELECT Recommendation_has_Context_Of_Use.`Recommendation_idRecommendation` FROM Recommendation_has_Context_Of_Use)) OR (Context_Of_Use.`idContext_Of_Use` ".$contextOfUseIds.")) AND ((Recommendation.`idRecommendation` NOT IN (SELECT Recommendation_has_Age_Group.`Recommendation_idRecommendation` FROM Recommendation_has_Age_Group)) OR (Age_Group.`idAge_Group` ".$ageIds.")) AND Disability.`idDisability` ".$disabilityIds.";";
	}

}
