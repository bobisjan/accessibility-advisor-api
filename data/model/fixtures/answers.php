<?php



$questions = array();

$questions[] = array(
    'id' => 1,
    'disabled' => array(2, 3, 4, 5, 6),
    'sql' => "SELECT Impairment.idImpairment AS id, Impairment.title AS title FROM Impairment ORDER BY Impairment.idImpairment;"
);

$questions[] = array(
    'id' => 2,
    'disabled' => array(),
    'sql' => "SELECT Disability.idDisability AS id, Disability.ICFCodeQualifiedTitle AS title FROM Disability WHERE Impairment_idImpairment = 1;"
);

$questions[] = array(
    'id' => 3,
    'disabled' => array(),
    'sql' => "SELECT Age_Group.idAge_Group as id, Age_Group.groupName AS title FROM Age_Group;"
);

$questions[] = array(
    'id' => 4,
    'disabled' => array(),
    'sql' => "SELECT Context_Of_Use.idContext_Of_Use AS id, Context_Of_Use.title AS title FROM Context_Of_Use WHERE contextCathegory='lighting';"
);

$questions[] = array(
    'id' => 5,
    'disabled' => array(),
    'sql' => "SELECT Context_Of_Use.idContext_Of_Use AS id, Context_Of_Use.title AS title FROM Context_Of_Use WHERE contextCathegory='noisiness'"
);

$questions[] = array(
    'id' => 6,
    'disabled' => array(),
    'sql' => "SELECT Application_Type.idApplication_Type AS id, Application_Type.title AS title FROM Application_Type"
);



function answers($sql) {
	global $database;
	$answers = array();
	
	foreach ($database->fetchAll($sql) as $value) {
	    $answers[] = Nette\ArrayHash::from($value);
	}
	return $answers;
}



$answers = array();
$id = 1;

foreach ($questions as $value) {
    foreach (answers($value['sql']) as $item) {
    	$answer = new Nette\ArrayHash();
    	$answer->id = $id;
    	$answer->title = $item->title;
    	$answer->disabled = in_array($id, $value['disabled']);
    	$answer->question_id = $value['id'];
    	
    	$answer->backend = $item->id;
    	
    	$answers[$id] = $answer;
    	$id++;
    }
}

return $answers;
