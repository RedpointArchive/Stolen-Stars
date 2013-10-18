<?php
require 'include.php';

$place_id = $_POST['target'];
$player_id = $_GET['id'];

$stmt = $db->prepare("
SELECT
  player.name AS name,
  place.id AS current_id,
  place.name AS current_place
FROM player
LEFT JOIN place ON place.id = player.place_id
WHERE player.id = :id");
$stmt->bindValue(':id', $player_id);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such player.');

$player_name = $result["name"];
$current_place_id = $result["current_id"];
$current_place = $result["current_place"];

if ($current_place_id == $place_id) {
  // No op.
  header("Location: /player.php?id=".$player_id);
  die();
}

$stmt = $db->prepare("
SELECT name FROM place
WHERE id = :id");
$stmt->bindValue(':id', $place_id);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such place.');

$new_place = $result["name"];

$stmt = $db->prepare("
UPDATE player
SET place_id = :target
WHERE id = :id");
$stmt->bindValue(':target', $place_id);
$stmt->bindValue(':id', $player_id);
$stmt->execute();

create_log($db, "$player_name moved from $current_place to $new_place");

header("Location: /player.php?id=".$player_id);