<?php

namespace AccessibilityAdvisor;

use Nette;



/**
 * Question.
 *
 * @author Jan Bobisud
 */
class Question extends Nette\Object
{

	const TYPE_RADIO    = 0;
	const TYPE_CHECKBOX = 1;
	const TYPE_SELECT   = 2;



	/** @var Store */
	private $store;

	/** @var array */
	private $questions;



	public function __construct(Store $store, $fixtures = array())
	{
		$this->store = $store;
		$this->questions = array();
		
		foreach ($fixtures as $fixture) {
			$this->questions[] = $fixture;
		}
	}
	
	
	
	public function find($id)
	{
		foreach ($this->questions as $question) {
			if ($question->id == $id) {
				return $question;
			}
		}
		return NULL;
	}



	public function findAll()
	{
		return $this->questions;
	}

	
		
	public function findMany(array $ids) {
		$items = array();
		
		foreach ($this->questions as $question) {
			if (in_array($question->id, $ids)) {
				$items[] = $question;
			}
		}
		return $items;
	}

}