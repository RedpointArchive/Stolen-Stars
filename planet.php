<?php
require 'include.php';

$stmt = $db->prepare("
SELECT 
  planet.id,
  planet.name,
  planet.notes,
  planet.system_id
FROM planet
WHERE planet.id = :id");
$stmt->bindValue(':id', $_GET['id']);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such planet.');

?>
<h1><?php echo $result['name']; ?></h1>
<p><?php echo nl2br($result['notes']); ?></p>
<a href="/system.php?id=<?php echo $result['system_id']; ?>">Back</a> &bull; 
<?php CRUD::renderEditLink("planet", $result['id']); ?> &bull; 
<?php CRUD::renderNewLink("planet"); ?>
<h2>Places</h2>
<ul>
<?php
$stmt = $db->prepare("SELECT * FROM place WHERE planet_id = :id");
$stmt->bindValue(':id', $_GET['id']);
$stmt->execute();
while ($row = $stmt->fetch()) {
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