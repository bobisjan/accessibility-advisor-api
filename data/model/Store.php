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
    private $group;

    /** @var Question */
    private $question;

    /** @var Answer */
    private $answer;

    /** @var Result */
    private $result;

    /** @var Recommendation */
    private $recommendation;

    /** @var Persona */
    private $persona;

    /** @var Tool */
    private $tool;



    public function __construct(Nette\Database\Connection $database)
    {
        $this->database = $database;
        require __DIR__ . '/fixtures/fixtures.php';
        $this->fixtures = loadFixtures($this->database);
    }



    public function getDatabase()
    {
        return $this->database;
    }



    public function getGroups()
    {
        if (!$this->group) {
            $this->group = new Group($this, $this->fixtures['groups']);
        }
        return $this->group;
    }



    public function getQuestions()
    {
        if (!$this->question) {
            $this->question = new Question($this, $this->fixtures['questions']);
        }
        return $this->question;
    }



    public function getAnswers()
    {
        if (!$this->answer) {
            $this->answer = new Answer($this, $this->fixtures['answers']);
        }
        return $this->answer;
	}



    public function getResult()
    {
        if (!$this->result) {
            $this->result = new Result($this);
        }
        return $this->result;
    }



    public function getRecommendations()
    {
        if (!$this->recommendation) {
            $this->recommendation = new Recommendation($this);
        }
        return $this->recommendation;
    }



    public function getPersonas()
    {
        if (!$this->persona) {
            $this->persona = new Persona($this);
        }
        return $this->persona;
    }



    public function getTools()
    {
        if (!$this->tool) {
            $this->tool = new Tool($this);
        }
        return $this->tool;
    }

}
