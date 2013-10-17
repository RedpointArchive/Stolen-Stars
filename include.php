<?php

date_default_timezone_set("Australia/Melbourne");

include 'config.php';
include 'dba/dao.php';
include 'lib/log.php';
include 'lib/player.php';
include 'lib/planet.php';
include 'lib/place.php';
include 'lib/ship.php';
include 'lib/skill.php';
include 'lib/stats.php';
include 'lib/inventory.php';

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

?>
