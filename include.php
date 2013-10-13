<?php

if (!file_exists("db.sqlite3")) {
  $db = new SQLite3("db.sqlite3");
  $db->exec(file_get_contents("init.sql"));
  $db->close();
}

$db = new SQLite3("db.sqlite3");
$version = $db->query("SELECT version FROM info;")->fetchArray();
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

function list_ships($db, $where = "", $param = null) {
  echo '<h1>Ships</h1>';
  $stmt = $db->prepare("
SELECT
  ship.name as ship_name,
  ship.id as ship_id
FROM ship
".$where."
ORDER BY ship.name ASC");
  if ($param !== null) {
    $stmt->bindValue(':param', $param);
  }
  $results = $stmt->execute();
  $output = false;
  while ($row = $results->fetchArray()) {
    if (!$output) {
      echo '<ul>';
      $output = true;
    }
    echo '<li><a href="/ship.php?id='.$row["ship_id"].'">'.
      $row["ship_name"].'</a>'.
      '</li>';
  }
  if ($output) {
    echo '</ul>';
  } else {
    echo 'There are no ships in this region.';
  }
}

function list_players($db, $where = "", $param = null) {
  echo '<h1>Players</h1>';
  $stmt = $db->prepare("
SELECT
  party.name as party_name,
  player.name as player_name,
  place.name as place_name,
  player.id as player_id,
  place.id as place_id
FROM party
LEFT JOIN player ON player.party_id = party.id
LEFT JOIN place ON player.place_id = place.id
".$where."
ORDER BY party.name ASC");
  if ($param !== null) {
    $stmt->bindValue(':param', $param);
  }
  $results = $stmt->execute();
  $count = 0;
  $pname = null;
  while ($row = $results->fetchArray()) {
    $count++;
    if ($pname != $row["party_name"]) {
      if ($pname != null) {
        echo '</ul>';
      }
      $pname = $row["party_name"];
      echo '<h2>'.$pname.'</h2><ul>';
    }
    echo '<li><a href="/player.php?id='.$row["player_id"].'">'.
      $row["player_name"].'</a> (at '.
      '<a href="/place.php?id='.$row["place_id"].'">'.$row["place_name"].'</a>)'.
      '</li>';
  }
  if ($pname != null) {
    echo '</ul>';
  }
  if ($count === 0) {
    echo 'There are no players in this region.';
  }
}

function get_grouped_places($db) {
  $places = $db->query("
SELECT place.id, place.name, planet.name AS planet_name
FROM place
LEFT JOIN planet
  ON planet.id = place.planet_id
ORDER BY planet.name ASC;");
  $results = array();
  $pname = null;
  while ($row = $places->fetchArray()) {
    if (!array_key_exists($row["planet_name"], $results)) {
      $results[$row["planet_name"]] = array();
    }
    $results[$row["planet_name"]][] = array(
      'id' => $row["id"],
      'name' => $row["name"]);
  }
  return $results;
}

?>
