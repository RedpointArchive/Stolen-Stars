<?php
include 'include.php';

$allowed = array(
  'planet',
  'place',
  'system',
  'ship');

if (!in_array($_GET['class'], $allowed)) {
  die('Not allowed to edit this object!');
}

$crud = new CRUD(
  $db,
  $_GET['class'],
  $_GET['id']);
if (array_key_exists('r', $_GET)) {
  $crud->setReturnURL($_GET['r']);
}

switch ($_GET['class']) {
  case "planet":
    $crud->setEditor("name", CRUD::EDITOR_TEXT);
    $crud->setEditor("notes", CRUD::EDITOR_DESCRIPTION);
    $crud->setEditor("leadership", CRUD::EDITOR_TEXT);
    $crud->setEditor("category", CRUD::EDITOR_TEXT);
    $crud->setLogSource("name");
    break;
  case "place":
    $crud->setEditor("name", CRUD::EDITOR_TEXT);
    $crud->setEditor("planet_id", CRUD::EDITOR_LOOKUP);
    $crud->setEditor("notes", CRUD::EDITOR_DESCRIPTION);
    $crud->setLogSource("name");
    break;
  case "system":
    $crud->setEditor("name", CRUD::EDITOR_TEXT);
    $crud->setEditor("notes", CRUD::EDITOR_DESCRIPTION);
    $crud->setLogSource("name");
    break;
  case "ship":
    $crud->setEditor("name", CRUD::EDITOR_TEXT);
    $crud->setLogSource("name");
    break;
}
  
$crud->handleEdit();
