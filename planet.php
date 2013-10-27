<?php
require 'include.php';

$planet = new Planet($db);
$planet->load($_GET['id']);

$header = getOwnershipDetails($planet) . " " . $planet->getName();

?>
<h1><?php echo $header; ?></h1>
<p><?php echo nl2br($planet->getNotes()); ?></p>
<a href="/system.php?id=<?php echo $planet->getSystemID(); ?>">Back</a> &bull; 
<?php CRUD::renderEditLink("planet", $planet->getID()); ?> &bull; 
<?php CRUD::renderNewLink("planet"); ?>
<?php renderOwnershipLinks($planet); ?>
<h2>Orbital Map</h2>
<?php render_orbit_map('planet_id', $planet->getID()); ?>
<h2>Places</h2>
<ul>
<?php
$stmt = $db->prepare("SELECT * FROM place WHERE planet_id = :id");
$stmt->bindValue(':id', $planet->getID());
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
WHERE planet.id = :param", $planet->getID()); ?>
<?php list_players($db, "
WHERE place.planet_id = :param", $planet->getID()); ?>