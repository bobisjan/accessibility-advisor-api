<?php

namespace AccessibilityAdvisor;

use Nette;



/**
 * Tool.
 *
 * @author Jan Bobisud
 */
class Tool extends Nette\Object
{

	/** @var Store */
	private $store;



	public function __construct(Store $store)
	{
		$this->store = $store;
	}



	public function findAll($query = NULL)
	{
		$database = $this->store->database;
		$tools = array();
		$recommendations = (isset($query['recommendation']) && is_array($query['recommendation']))
		                    ? $query['recommendation']
		                    : array();
		
		foreach ($database->fetchAll($this->sqlForTools($recommendations)) as $id) {
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
	
	
	
	private function sqlForTools(array $recommendations = array())
	{
		$sql = "SELECT DISTINCT Developer_Tool.`idDeveloper_Tool` FROM (Recommendation LEFT JOIN Recommendation_has_Developer_Tool ON Recommendation.`idRecommendation`=Recommendation_has_Developer_Tool.`Recommendation_idRecommendation`)
		             LEFT JOIN Developer_Tool ON Recommendation_has_Developer_Tool.`Developer_Tool_idDeveloper_Tool`=Developer_Tool.`idDeveloper_Tool` ";
		 
        if (count($recommendations) > 0) {
            $sql .= " WHERE Recommendation.`idRecommendation` IN (" . implode(", ", $recommendations) . ") ";
            $sql .= " AND Developer_Tool.`idDeveloper_Tool` IS NOT NULL ; ";
        } else {
            $sql .= " WHERE Developer_Tool.`idDeveloper_Tool` IS NOT NULL ; ";
        }
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

}