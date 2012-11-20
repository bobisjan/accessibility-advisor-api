<?php

$database;



function loadFixtures(Nette\Database\Connection $connection) {
	global $database;
	$database = $connection;
	$fixtures = array();

	$fixtures['groups']    = require __DIR__ . '/groups.php';
	$fixtures['questions'] = require __DIR__ . '/questions.php';
	$fixtures['answers']   = require __DIR__ . '/answers.php';
	
	foreach ($fixtures['answers'] as $answer) {
		$fixtures['questions'][$answer->question_id]->answers[] = $answer->id;
	}
	
	return $fixtures;
}
