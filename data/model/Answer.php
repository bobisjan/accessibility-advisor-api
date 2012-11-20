<?php

namespace AccessibilityAdvisor;

use Nette;



/**
 * Answer.
 *
 * @author Jan Bobisud
 */
class Answer extends Nette\Object
{

	/** @var Store */
	private $store;

	/** @var array */
	private $answers;



	public function __construct(Store $store, $fixtures = array())
	{
		$this->store = $store;
		$this->answers = array();
		
		foreach ($fixtures as $fixture) {
			$this->answers[] = $fixture;
		}
	}
	
	
	
	public function find($id) {
		foreach ($this->answers as $answer) {
			if ($answer->id == $id) {
				return $answer;
			}
		}
		return NULL;
	}



	public function findAll()
	{
		return $this->answers;
	}
	
	
	
	public function findMany(array $ids) {
		$items = array();
		
		foreach ($this->answers as $answer) {
			if (in_array($answer->id, $ids)) {
				$items[] = $anwser;
			}
		}
		return $items;
	}

}
