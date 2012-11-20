<?php

$groups = array();



/* ******************** Group ******************** */

$group = new Nette\ArrayHash();
$groups[1] = $group;

$group->id = 1;
$group->title = "Application Type";
$group->description = "";
$group->questions = array(6);
$group->editLabelText = "Change application type";



/* ******************** Group ******************** */

$group = new Nette\ArrayHash();
$groups[2] = $group;

$group->id = 2;
$group->title = "User Characteristics";
$group->description = "On this tab you are going to be asked about the main characteristics of the user you are designing for, his impairment and other factors that influence his interaction with the system. Start filling out the form by selecting any question. Click into the area of the question will display appropriate description, help, case study or video/audio sample here in this box area.";
$group->questions = array(1, 2, 3);

$group->editLabelText = "Change user characteristics";
$group->continueText = "In the next step you are going to be asked about the context of use of your application.";



/* ******************** Group ******************** */

$group = new Nette\ArrayHash();
$groups[3] = $group;

$group->id = 3;
$group->title = "Context of Use";
$group->description = "On this tab you are going to specify the context of use of your application. Start filling out the form by selecting a question. Click into the area of the question will display appropriate description, help, case study or video/audio sample here in this box area.";
$group->questions = array(4, 5);

$group->editLabelText = "Change context of use";
$group->continueText = "Continue to a summary of what you have selected.";

return $groups;
