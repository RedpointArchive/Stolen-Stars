<?php
require '../include.php';

$crud = new CRUD(
  $db,
  'user',
  $_GET['id']);
if (array_key_exists('r', $_GET)) {
  $crud->setReturnURL($_GET['r']);
}

$crud->setEditor("username", CRUD::EDITOR_TEXT);
$crud->setEditor("email", CRUD::EDITOR_TEXT);
$crud->setEditor("password", CRUD::EDITOR_PASSWORD);
$crud->setEditor("application", CRUD::EDITOR_DESCRIPTION);
$crud->setEditor("approved", CRUD::EDITOR_BOOLEAN);
$crud->setEditor("is_administrator", CRUD::EDITOR_BOOLEAN);
$crud->setLogSource("username");
  
$crud->handleEdit();
