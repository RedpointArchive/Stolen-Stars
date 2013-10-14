<?php

date_default_timezone_set("Australia/Melbourne");

include 'config.php';
include 'lib/log.php';
include 'lib/player.php';
include 'lib/planet.php';
include 'lib/place.php';
include 'lib/ship.php';
include 'lib/skill.php';
include 'lib/stats.php';

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
  for ($i = $current + 1; $i <= $max; $i++) {
    $db->exec(file_get_contents("patch/$i.sql"));
    echo "Applied SQL patch $i...<br />";
  }
}

?>
