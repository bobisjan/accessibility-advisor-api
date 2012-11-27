<?php

namespace AccessibilityAdvisor;

use Nette;



/**
 * Persona.
 *
 * @author Jan Bobisud
 */
class Persona extends Nette\Object
{

	/** @var Store */
	private $store;



	public function __construct(Store $store)
	{
		$this->store = $store;
	}



	public function findAll(array $query = array())
	{
	    $database = $this->store->database;
	    $personas = array();
	    $disabilities = (isset($query['disability']) && is_array($query['disability']))
	                    ? $query['disability']
	                    : array();
	    
	    foreach($database->fetchAll($this->sqlForPersonas($disabilities)) as $row) {
	    	$personas[] = $this->createPersona($row, true);
	    }
	    foreach($database->fetchAll($this->sqlForPersonas($disabilities, FALSE)) as $row) {
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
	
	
	
	private function sqlForPersonas(array $disabilities = array(), $primary = TRUE)
	{
		$sql = "SELECT * FROM (SELECT *, COUNT(*) AS cc
	             FROM Persona
	               JOIN Persona_has_Disability ON Persona.`idPersona`=Persona_has_Disability.`Persona_idPersona`
	            	JOIN Disability ON Persona_has_Disability.`Disability_idDisability`=Disability.`idDisability` ";
	
	    if (count($disabilities) > 0) {
	        $sql .= " WHERE Disability.`idDisability` IN (" . implode(", ", $disabilities) . ") ";
	    }
	    $sql .= " GROUP BY Persona.`name`) AS T ORDER BY cc DESC LIMIT ";
		$sql .= ($primary) ? "1;" : "1,2;";
		return $sql;
	}

}