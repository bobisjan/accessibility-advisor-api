<?php

$questions = array();



/* ******************** Question ******************** */

$question = new Nette\ArrayHash();
$questions[1] = $question;

$question->id = 1;
$question->type = AccessibilityAdvisor\Question::TYPE_RADIO;
$question->title = "Impairment";
$question->description = "Impairment is defined as an alteration of an individual\'s health status - a deviation from normal in a body part or organ system and its functioning. If there are more than one impairments, please select the major one. Major impairment is the one which influences the ability of controlling the device/system the most.";
$question->answers = array();
$question->group_id = 2;
$question->validators = "not_empty";



/* ******************** Question ******************** */

$question = new Nette\ArrayHash();
$questions[2] = $question;

$question->id = 2;
$question->type = AccessibilityAdvisor\Question::TYPE_CHECKBOX;
$question->title = "Disability";
$question->description = "Disability is defined as an alteration of an individual\'s capacity to meet personal, social, or occupational demands because of an impairment. Disability as an activity limitation that creates a difficulty in the performance, accomplishment, or completion of an activity in the manner or within the range considered normal for a human being. Difficulty encompasses all of the ways in which the performance of the activity may be affected. In the list given, there are also diagnosis listed.";
$question->answers = array();
$question->group_id = 2;
$question->validators = "not_empty";



/* ******************** Question ******************** */

$question = new Nette\ArrayHash();
$questions[3] = $question;

$question->id = 3;
$question->type = AccessibilityAdvisor\Question::TYPE_CHECKBOX;
$question->title = "Age";
$question->description = "User needs, abilities and performance changes significantly during the life of the user. If possible identify please the specific age groups.";
$question->answers = array();
$question->group_id = 2;
$question->validators = "not_empty"; // "{validate:function(value){if(value.length>3){return {type:Validators.RESULT_TYPE_WARNING,text:'Are you sure you cannot narrow your selection? The more specific you are, the better recommendations you will get.'};}return true;}}"



/* ******************** Question ******************** */

$question = new Nette\ArrayHash();
$questions[4] = $question;

$question->id = 4;
$question->type = AccessibilityAdvisor\Question::TYPE_CHECKBOX;
$question->title = "Lighting Conditions";
$question->description = "Select one or two lighting conditions that are most typical for use of your application. Different environments have specific characteristics that have to be taken into account. Please specify the most characteristic environment where the system will be used. E.g. in outdoor environment there could be noisy and light conditions are changing rapidly. Privacy of the user could be also threatened.";
$question->answers = array();
$question->group_id = 3;
$question->validators = "not_empty"; // "{validate:function(value){if(value.length>2){return {type:Validators.RESULT_TYPE_WARNING,text:'Are you sure you cannot narrow your selection? The more specific you are, the better recommendations you will get.'};}return true;}}"



/* ******************** Question ******************** */

$question = new Nette\ArrayHash();
$questions[5] = $question;

$question->id = 5;
$question->type = AccessibilityAdvisor\Question::TYPE_CHECKBOX;
$question->title = "Environment Noisiness";
$question->description = "Select one or two lighting conditions that are most typical for use of your application. Different environments have specific characteristics that have to be taken into account. Please specify the most characteristic environment where the system will be used. E.g. in outdoor environment there could be noisy and light conditions are changing rapidly. Privacy of the user could be also threatened.";
$question->answers = array();
$question->group_id = 3;
$question->validators = "not_empty"; // "{validate:function(value){if(value.length>2){return {type:Validators.RESULT_TYPE_WARNING,text:'Are you sure you cannot narrow your selection? The more specific you are, the better recommendations you will get.'};}return true;}}"



/* ******************** Question ******************** */

$question = new Nette\ArrayHash();
$questions[6] = $question;

$question->id = 6;
$question->type = AccessibilityAdvisor\Question::TYPE_SELECT;
$question->title = "Application Type";
$question->description = "";
$question->answers = array();
$question->group_id = 1;
$question->validators = "not_empty";



return $questions;
