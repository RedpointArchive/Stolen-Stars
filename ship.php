<?php
include 'include.php';

$stmt = $db->prepare("
SELECT 
  ship.id,
  ship.place_id,
  ship.system_id,
  ship.name,
  place.notes AS notes,
  planet.id AS planet_id,
  planet.name AS planet_name,
  system.id AS system_id,
  system.name AS system_name
FROM ship
LEFT JOIN place ON place.id = ship.place_id
LEFT JOIN planet ON planet.id = place.planet_id
LEFT JOIN system ON system.id = ship.system_id
WHERE ship.id = :id");
$stmt->bindValue(':id', $_GET['id']);
$results = $stmt->execute();
$result = $results->fetchArray();
if (!$result) die('No such ship.');

?>
<h1><?php echo $result['name']; ?> (Ship)</h1>
<?php
if ($result['planet_id'] !== null) {
?>
<p>
  <em>Currently landed on 
    <a href="/planet.php?id=<?php echo $result['planet_id']; ?>">
      <?php echo $result['planet_name']; ?>
    </a>
  </em>
</p>
<?php
} else if ($result['system_id'] !== null) {
?>
<p>
  <em>Currently travelling through 
    <a href="/system.php?id=<?php echo $result['system_id']; ?>">
      <?php echo $result['system_name']; ?>
    </a>
  </em>
</p>
<?php
}
?>
<p><?php echo nl2br($result['notes']); ?></p>
<?php list_players($db, "
WHERE player.place_id = :param", $result['place_id']); ?>