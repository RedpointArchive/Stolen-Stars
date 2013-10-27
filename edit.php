<?php
require 'include.php';

$allowed = array(
  'planet',
  'place',
  'system',
  'ship',
  'journal');

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
    $crud->setEditor("system_id", CRUD::EDITOR_LOOKUP);
    $crud->setEditor("notes", CRUD::EDITOR_DESCRIPTION);
    $crud->setEditor("leadership", CRUD::EDITOR_TEXT);
    $crud->setEditor("category", CRUD::EDITOR_TEXT);
    $crud->setEditor("size", CRUD::EDITOR_NUMBER);
    $crud->setEditor("color", CRUD::EDITOR_COLOR);
    $crud->setEditor("orbit_distance", CRUD::EDITOR_NUMBER);
    $crud->setEditor("orbit_position", CRUD::EDITOR_PERCENT);
    $crud->setEditor("orbit_parent_planet_id", CRUD::EDITOR_LOOKUP);
    $crud->allowNull("orbit_parent_planet_id");
    $crud->requireManage("name");
    $crud->requireManage("system_id");
    $crud->requireManage("leadership");
    $crud->requireManage("category");
    $crud->requireManage("size");
    $crud->requireManage("color");
    $crud->requireManage("orbit_distance");
    $crud->requireManage("orbit_position");
    $crud->requireManage("orbit_parent_planet_id");
    $crud->setLogSource("name");
    break;
  case "place":
    $crud->setEditor("name", CRUD::EDITOR_TEXT);
    $crud->setEditor("planet_id", CRUD::EDITOR_LOOKUP);
    $crud->setEditor("notes", CRUD::EDITOR_DESCRIPTION);
    $crud->requireManage("name");
    $crud->requireManage("planet_id");
    $crud->setLogSource("name");
    break;
  case "system":
    $crud->setEditor("name", CRUD::EDITOR_TEXT);
    $crud->setEditor("notes", CRUD::EDITOR_DESCRIPTION);
    $crud->requireManage("name");
    $crud->setLogSource("name");
    break;
  case "ship":
    $crud->setEditor("name", CRUD::EDITOR_TEXT);
    $crud->requireManage("name");
    $crud->setLogSource("name");
    break;
  case "journal":
    $crud->setEditor("player_id", CRUD::EDITOR_LOOKUP);
    $crud->setEditor("created", CRUD::EDITOR_DATE);
    $crud->setEditor("content", CRUD::EDITOR_DESCRIPTION);
    $crud->setLogSource(function($journal) {
      return $journal->getPlayer()->getName();
    });
    $crud->setFriendlyName("created", "Logged");
    if ($_GET['id'] == -1) {
      $crud->setDefault("player_id", $_GET['player_id']);
      $crud->setDefault("created", time());
    }
    $crud->setEditMessage("", "'s journal was updated");
    break;
}
  
$crud->handleEdit();
