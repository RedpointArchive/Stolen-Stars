<?php
require '../include.php';

$crud = new CRUD(
  $db,
  'itemdao',
  $_GET['id']);
if (array_key_exists('r', $_GET)) {
  $crud->setReturnURL($_GET['r']);
}

$crud->setEditor("name", CRUD::EDITOR_TEXT);
$crud->setEditor("has_quantity", CRUD::EDITOR_BOOLEAN);
$crud->setLogSource("name");
  
$crud->handleEdit();
