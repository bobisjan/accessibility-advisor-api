<?php

$answers = array();



function answers($sql) {
	global $database;
	$answers = array();
	
	foreach ($database->fetchAll($sql) as $value) {
	    $answers[] = $value->title;
	}
	return $answers;
}



/* ******************** Answer ******************** */

$id = 1;
$i = 1;
$question = 1;
$disabled = array(2, 3, 4, 5, 6);
$sql = "SELECT Impairment.title AS title FROM Impairment ORDER BY Impairment.idImpairment;";

foreach (answers($sql) as $item) {
	$answer = new Nette\ArrayHash();
	$answer->id = $id;
	$answer->title = $item;
	$answer->disabled = in_array($id, $disabled);
	$answer->question_id = $question;
	
	$answer->backend = $i;
	$i++;
	
	$answers[$id] = $answer;
	$id++;
}




/* ******************** Answer ******************** */

$i = 108;
$question = 2;
$disabled = array();
$sql = "SELECT Disability.ICFCodeQualifiedTitle AS title FROM Disability WHERE Impairment_idImpairment = 1;";

foreach (answers($sql) as $item) {
	$answer = new Nette\ArrayHash();
	$answer->id = $id;
	$answer->title = $item;
	$answer->disabled = in_array($id, $disabled);
	$answer->question_id = $question;
	
	$answer->backend = $i;
	$i++;
	
	$answers[$id] = $answer;
	$id++;
}



/* ******************** Answer ******************** */

$i = 1;
$question = 3;
$disabled = array();
$sql = "SELECT Age_Group.groupName AS title FROM Age_Group;";

foreach (answers($sql) as $item) {
	$answer = new Nette\ArrayHash();
	$answer->id = $id;
	$answer->title = $item;
	$answer->disabled = in_array($id, $disabled);
	$answer->question_id = $question;
	
	$answer->backend = $i;
	$i++;
	
	$answers[$id] = $answer;
	$id++;
}



/* ******************** Answer ******************** */

$i = 1;
$question = 4;
$disabled = array();
$sql = "SELECT Context_Of_Use.title AS title FROM Context_Of_Use WHERE contextCathegory='lighting';";

foreach (answers($sql) as $item) {
	$answer = new Nette\ArrayHash();
	$answer->id = $id;
	$answer->title = $item;
	$answer->disabled = in_array($id, $disabled);
	$answer->question_id = $question;
	
	$answer->backend = $i;
	$i++;
	
	$answers[$id] = $answer;
	$id++;
}



/* ******************** Answer ******************** */

$question = 5;
$disabled = array();
$sql = "SELECT Context_Of_Use.title AS title FROM Context_Of_Use WHERE contextCathegory='noisiness'";

foreach (answers($sql) as $item) {
	$answer = new Nette\ArrayHash();
	$answer->id = $id;
	$answer->title = $item;
	$answer->disabled = in_array($id, $disabled);
	$answer->question_id = $question;
	
	$answer->backend = $i;
	$i++;
	
	$answers[$id] = $answer;
	$id++;
}



/* ******************** Answer ******************** */

$i = 1;
$question = 6;
$disabled = array();

foreach (array("Desktop application", "Web application", "Mobile application") as $item) {
	$answer = new Nette\ArrayHash();
	$answer->id = $id;
	$answer->title = $item;
	$answer->disabled = in_array($id, $disabled);
	$answer->question_id = $question;
	
	$answer->backend = $i;
	$i++;
	
	$answers[$id] = $answer;
	$id++;
}



return $answers;
