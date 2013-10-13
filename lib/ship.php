<?php

function list_ships($db, $where = "", $param = null) {
  echo '<h2>Ships</h2>';
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

?>