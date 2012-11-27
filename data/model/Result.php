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



	public function __construct(Store $store)
	{
		$this->store = $store;
	}



	public function findAll(array $query = array())
	{
		$result = new Nette\ArrayHash();
		$result->recommendations = $this->store->getRecommendations()->findAll($query);
		
		$ids = array();
		foreach ($result->recommendations as $value) {
		    if (isset($value->id)) {
		        $ids[] = $value->id;
		    }
		}
		
		$result->personas = $this->store->getPersonas()->findAll($query);
		$result->tools = $this->store->getTools()->findAll($ids);
//		$result->devices = $this->devices();
//		$result->model = $this->model();
		
		return $result;
	}

}
