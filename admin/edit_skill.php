<?php
require '../include.php';

$crud = new CRUD(
  $db,
  'skill',
  $_GET['id']);
if (array_key_exists('r', $_GET)) {
  $crud->setReturnURL($_GET['r']);
}

$crud->setEditor("name", CRUD::EDITOR_TEXT);
$crud->setEditor("parent_id", CRUD::EDITOR_LOOKUP);
$crud->allowNull("parent_id");
$crud->setLogSource("name");
  
$crud->handleEdit();
