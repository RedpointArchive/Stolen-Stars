<?php
require 'include.php';

$ship_id = $_GET['id'];

$stmt = $db->prepare("
SELECT
  ship.place_id,
  ship.name,
  planet.system_id,
  planet.name AS planet_name,
  system.name AS system_name
FROM ship
LEFT JOIN place ON place.id = ship.place_id
LEFT JOIN planet ON planet.id = place.planet_id
LEFT JOIN system ON planet.system_id = system.id
WHERE ship.id = :id");
$stmt->bindValue(':id', $ship_id);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such ship.');

$ship_name = $result["name"];
$planet_name = $result["planet_name"];
$system_name = $result["system_name"];

$stmt = $db->prepare("
UPDATE place
SET planet_id = null
WHERE id = :place_id;");
$stmt->bindValue(':place_id', $result['place_id']);
$stmt->execute();

$stmt = $db->prepare("
UPDATE ship
SET system_id = :system_id
WHERE id = :ship_id;");
$stmt->bindValue(':system_id', $result['system_id']);
$stmt->bindValue(':ship_id', $ship_id);
$stmt->execute();

create_log($db, "$ship_name took off from $planet_name and is now in the $system_name system");

header("Location: /ship.php?id=".$ship_id);