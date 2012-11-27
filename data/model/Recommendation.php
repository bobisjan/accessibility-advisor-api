<?php

namespace AccessibilityAdvisor;

use Nette;



/**
 * Recommendation.
 *
 * @author Jan Bobisud
 */
class Recommendation extends Nette\Object
{

	/** @var Store */
	private $store;



	public function __construct(Store $store)
	{
		$this->store = $store;
	}



	public function findAll($query)
	{
		$database = $this->store->database;
		list($applicationTypeId, $disabilityIds, $contextOfUseIds, $ageIds) = $this->prepareParameters($query);
		
		$rows = $database->fetchAll(
			$this->getRecommendationsSql($applicationTypeId, $contextOfUseIds, $ageIds, $disabilityIds));

//		$this->ids = array();
		$recommendations = array();
		
		$sql = "SELECT * FROM Recommendation WHERE idRecommendation = ?;";
		
		foreach ($rows as $row) {
			$id = $row['idRecommendation'];
//			$this->ids[] = $id;
			
			foreach ($database->fetchAll($sql, $id) as $value) {				
				$recommendations[] = $this->createRecomendation($value,
					$this->parameters($this->getImpairmentSql($id), 'title'),
					$this->parameters($this->getDisabilitySql($id, $disabilityIds), 'ICFCodeQualifiedTitle'),
					$this->parameters($this->getAgeSql($id, $ageIds), 'groupName'),
					$this->parameters($this->getNoiseSql($id, $contextOfUseIds), 'title'),
					$this->parameters($this->getLightSql($id, $contextOfUseIds), 'title'));
			}
		}
		
		$recommendations[] = $this->generalRecommendations();
		return $recommendations;
	}
	
	
	
	private function createRecomendation($row, $impairments, $disabilities, $ages, $noises, $lights)
	{
		$recomendation = new Nette\ArrayHash();
		
		$recomendation->id = $row->idRecommendation;
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
	
	
	
	private function prepareParameters($parameters)
	{
		$params = array();
		
		$params[] = $parameters['application-type'];
		
		if (isset($parameters['disability'])) {
			$params[] = "IN (" . implode(", ", $parameters['disability']) . ")";
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
			$params[] = "IN (" . implode(", ", $ps) . ")";
		} else {
			$params[] = NULL;
		}
		
		if (isset($parameters['age'])) {
			$params[] = "IN (" . implode(", ", $parameters['age']) . ")";
		} else {
			$params[] = NULL;
		}

		return $params;
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
	
	
	
	private function generalRecommendations()
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
	    
	    $general = new Nette\ArrayHash();
	    $general->title = "General recommendations and accessibility rules";
	    $general->isGeneral = TRUE;
	    $general->content = $items;
	    
	    return $general;
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