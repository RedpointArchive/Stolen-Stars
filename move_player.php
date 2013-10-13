<?php
include 'include.php';

$place_id = $_POST['target'];
$player_id = $_GET['id'];

$stmt = $db->prepare("
UPDATE player
SET place_id = :target
WHERE id = :id");
$stmt->bindValue(':target', $place_id);
$stmt->bindValue(':id', $player_id);
$stmt->execute();

header("Location: /player.php?id=".$player_id);