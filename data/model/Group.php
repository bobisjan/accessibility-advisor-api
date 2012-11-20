<?php

namespace AccessibilityAdvisor;

use Nette;



/**
 * Group.
 *
 * @author Jan Bobisud
 */
class Group extends Nette\Object
{

	/** @var Store */
	private $store;

	/** @var array */
	private $groups;



	public function __construct(Store $store, $fixtures = array())
	{
		$this->store = $store;
		$this->groups = array();
		
		foreach ($fixtures as $fixture) {
			$this->groups[] = $fixture;
		}
	}



	public function findAll()
	{
		return $this->groups;
	}

}