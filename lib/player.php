<?php

function player_get_life_points($stats) {
  return $stats['strength'] + $stats['willpower'];
}

function player_get_initiative($stats) {
  return '1d'.$stats['agility'].' + 1d'.$stats['alertness'];
}

function list_players($db, $where = "", $param = null) {
  echo '<h2>Players</h2>';
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
ORDER BY party.name ASC, player.name ASC");
  if ($param !== null) {
    $stmt->bindValue(':param', $param);
  }
  $stmt->execute();
  $count = 0;
  $pname = null;
  while ($row = $stmt->fetch()) {
    $count++;
    if ($pname != $row["party_name"]) {
      if ($pname != null) {
        echo '</ul>';
      }
      $pname = $row["party_name"];
      echo '<h3>'.$pname.'</h3><ul>';
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

?>