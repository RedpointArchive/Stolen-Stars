<?php

function find_related_url($db, $stats_id) {
  $stmt = $db->prepare("
SELECT id
FROM player
WHERE stats_id = :id");
  $stmt->bindValue(":id", $stats_id);
  $stmt->execute();
  $result = $stmt->fetch();
  if ($result) return '/player.php?id='.$result["id"];
  return null;
}

function find_related_name($db, $stats_id) {
  $stmt = $db->prepare("
SELECT id, name
FROM player
WHERE stats_id = :id");
  $stmt->bindValue(":id", $stats_id);
  $stmt->execute();
  $result = $stmt->fetch();
  if ($result) return $result["name"];
  return null;
}

?>