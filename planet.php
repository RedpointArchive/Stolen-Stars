<?php
include 'include.php';

function get_system_id($db, $planet_id) {
  $stmt = $db->prepare("SELECT system_id FROM planet WHERE id = :id");
  $stmt->bindValue(':id', $planet_id);
  $results = $stmt->execute()->fetchArray();
  return $results["system_id"];
}
?>
<h1>Places</h1>
<a href="/system.php?id=<?php echo get_system_id($db, $_GET['id']); ?>">Back</a>
<ul>
<?php
$stmt = $db->prepare("SELECT * FROM place WHERE planet_id = :id");
$stmt->bindValue(':id', $_GET['id']);
$results = $stmt->execute();
while ($row = $results->fetchArray()) {
?>
<li>
  <a href="/place.php?id=<?php echo $row['id']; ?>">
    <?php echo $row["name"]; ?>
  </a>
</li>
<?php
}
?>
</ul>
<?php list_ships($db, "
LEFT JOIN place ON ship.place_id = place.id
LEFT JOIN planet ON planet.id = place.planet_id
WHERE planet.id = :param", $_GET['id']); ?>
<?php list_players($db, "
WHERE place.planet_id = :param", $_GET['id']); ?>