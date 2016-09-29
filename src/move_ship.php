<?php
require 'include.php';

$ship_id = $_GET['id'];
$planet_id = $_POST['target'];

$stmt = $db->prepare("
SELECT
  planet.name,
  system.name AS system_name
FROM planet
LEFT JOIN system ON system.id = planet.system_id
WHERE planet.id = :id");
$stmt->bindValue(':id', $planet_id);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such planet.');

$planet_name = $result["name"];
$system_name = $result["system_name"];

$stmt = $db->prepare("
SELECT
  ship.name,
  ship.place_id
FROM ship
WHERE ship.id = :id");
$stmt->bindValue(':id', $ship_id);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such ship.');

$ship_name = $result["name"];

$stmt = $db->prepare("
UPDATE place
SET planet_id = :planet_id
WHERE id = :place_id;");
$stmt->bindValue(':place_id', $result['place_id']);
$stmt->bindValue(':planet_id', $planet_id);
$stmt->execute();

$stmt = $db->prepare("
UPDATE ship
SET system_id = null
WHERE id = :ship_id;");
$stmt->bindValue(':ship_id', $ship_id);
$stmt->execute();

create_log($db, "$ship_name landed on $planet_name in the $system_name system");

header("Location: /ship.php?id=".$ship_id);