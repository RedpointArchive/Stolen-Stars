<?php

date_default_timezone_set("Australia/Melbourne");

chdir(realpath(dirname(__FILE__)));

require 'config.php';
require 'dba/dao.php';
require 'dba/crud.php';
require 'lib/log.php';
require 'lib/player.php';
require 'lib/planet.php';
require 'lib/place.php';
require 'lib/ship.php';
require 'lib/skill.php';
require 'lib/stats.php';
require 'lib/inventory.php';
require 'lib/system.php';
require 'lib/auth.php';

$db = ss_pdo_connect();

$query = $db->query("SELECT version FROM info;");
if ($query === false) {
  die("The database is corrupt!");
}
$version = $query->fetch();
$version = $version["version"];
$max = find_highest_patch();
if ($version < $max) {
  apply_patches($db, $version, $max);
  $stmt = $db->prepare("UPDATE info SET version = :new");
  $stmt->bindValue(":new", $max);
  $stmt->execute();
}

$auth = new Auth($db);
$auth_result = $auth->authorize();
if ($auth_result != Auth::SUCCESS) {
  if (!isset($allow_anonymous) || !$allow_anonymous) {
    $auth->handleFailure($auth_result);
  }
}

// If the path starts with /admin/, then the user must be
// an administrator to access the page.
if (substr($_SERVER['REQUEST_URI'], 0, 7) == '/admin/' ||
  $_SERVER['REQUEST_URI'] == '/admin') {
  if (!$auth->getUser()->getIsAdministrator()) {
    $_SESSION['error'] = "You don't have permission to access this page.";
    header('Location: /overview.php');
    die();
  }
}

if (isset($_SESSION['error'])) {
  echo '<p style="color: red;">'.$_SESSION['error'].'</p>';
  unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
  echo '<p style="color: green;">'.$_SESSION['success'].'</p>';
  unset($_SESSION['success']);
}

function find_highest_patch() {
  $files = scandir("patch");
  $max = 0;
  foreach ($files as $file) {
    if (substr($file, -4) == ".sql") {
      $val = (int)substr($file, 0, -4);
      if ($val > $max) {
        $max = $val;
      }
    }
  }
  return $max;
}

function apply_patches($db, $current, $max) {
  $name = ss_pdo_name();
  for ($i = $current + 1; $i <= $max; $i++) {
    if (file_exists("patch/$i.sql")) {
      $db->exec(
        ss_pdo_transform(
          file_get_contents("patch/$i.sql")));
      echo "Applied SQL patch $i...<br />";
    }
    if (file_exists("patch/".$i."_".$name.".sql")) {
      $db->exec(
        ss_pdo_transform(
          file_get_contents("patch/".$i."_".$name.".sql")));
      echo "Applied SQL patch $i ($name)...<br />";
    }
  }
}

// Output the user information in the top-right.
if (!$auth->isAnonymous()) {
?>
<div style="
  position: absolute;
  top: 20px;
  right: 20px;
  height: 50px;
  text-align: right;">
<?php echo $auth->getUser()->getUsername(); ?> &bull;
<?php if ($auth->getUser()->getIsAdministrator()) { ?>
<a href="/admin/">Admin</a> &bull;
<?php } ?>
<a href="/account.php">Account</a> &bull;
<a href="/logout.php">Logout</a>
</div>
<?php
}
?>
