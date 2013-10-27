<?php
include 'include.php';

switch ($_GET['type']) {
  case 'Planet':
  case 'Player':
  case 'Ship':
  case 'System':
  case 'Place':
    $type = $_GET['type'];
    $obj = new $type($db);
    $obj->load($_GET['id']);
    break;
  default:
    die('Invalid operation.');
    break;
}

function redirect() {
  header('Location: '.$_GET['r']);
  die();
}

switch ($_GET['mode']) {
  case 'take':
    // The user wants to take ownership, or take back the
    // object from the current GM.
    if ($obj->getOwnerID() === null) {
      // No current owner, assign the current user as the owner.
      $obj->setOwnerID($auth->getUser()->getID());
      $obj->save();
      create_log($db, $obj->getDisplayName().' was assigned to '.$auth->getUser()->getUsername());
      redirect();
    } else if ($obj->getOwnerID() === $auth->getUser()->getID()) {
      $obj->recover();
      create_log($db, $obj->getDisplayName().' was recovered by it\'s owner '.$auth->getUser()->getUsername());
      redirect();
    } else {
      die('Not authorized.');
    }
    break;
  case 'delegate':
    if ($obj->getOwnerID() === $auth->getUser()->getID() ||
        $obj->getGMID() === $auth->getUser()->getID()) {
      $obj->delegate($_POST['target']);
      create_log($db, $obj->getDisplayName().' was delegated to GM '.$obj->getGM()->getUsername());
      redirect();
    } else {
      die('Not authorized.');
    }
    break;
  default:
    die('Unknown mode.');
}

?>
