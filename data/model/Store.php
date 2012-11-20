<?php

namespace AccessibilityAdvisor;

use Nette;



/**
 * Store.
 *
 * @author Jan Bobisud
 */
class Store extends Nette\Object
{

    /** @var Nette\Database\Connection */
	private $database;
	
	/** @var array */
	private $fixtures;

	/** @var Group */
	private $groups;
	
	/** @var Question */
	private $questions;
	
	/** @var Answer */
	private $answers;
	
	/** @var Result */
	private $result;
	
	
	
	public function __construct(Nette\Database\Connection $database)
	{
		$this->database = $database;
		require __DIR__ . '/fixtures/fixtures.php';
		$this->fixtures = loadFixtures($this->database);
	}
	
	
	
	public function getGroups()
	{
		if (!$this->groups) {
			$this->groups = new Group($this, $this->fixtures['groups']);
		}
		return $this->groups;
	}
	
	
	
	public function getQuestions()
	{
		if (!$this->questions) {
			$this->questions = new Question($this, $this->fixtures['questions']);
		}
		return $this->questions;
	}
	
	
	
	public function getAnswers()
	{
		if (!$this->answers) {
			$this->answers = new Answer($this, $this->fixtures['answers']);
		}
		return $this->answers;
	}
	
	
	
	public function getResult()
	{
		if (!$this->result) {
			$this->result = new Result($this);
		}
		return $this->result;
	}
	
	
	
	public function getDatabase()
	{
		return $this->database;
	}

}
