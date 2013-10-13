<?php

function get_grouped_planets($db) {
  $places = $db->query("
SELECT planet.id, planet.name, system.name AS system_name
FROM planet
LEFT JOIN system
  ON system.id = planet.system_id
ORDER BY system.name ASC;");
  $results = array();
  $pname = null;
  while ($row = $places->fetchArray()) {
    if (!array_key_exists($row["system_name"], $results)) {
      $results[$row["system_name"]] = array();
    }
    $results[$row["system_name"]][] = array(
      'id' => $row["id"],
      'name' => $row["name"]);
  }
  return $results;
}

?>