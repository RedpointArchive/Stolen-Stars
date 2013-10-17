<?php

class Place extends DAO {
  protected $planet_id;
  protected $name;
  protected $notes;
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
  while ($row = $places->fetch()) {
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