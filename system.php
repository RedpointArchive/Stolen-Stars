<?php
require 'include.php';

$stmt = $db->prepare("
SELECT 
  system.id,
  system.name,
  system.notes
FROM system
WHERE system.id = :id");
$stmt->bindValue(':id', $_GET['id']);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such system.');

$stmt = $db->prepare("SELECT * FROM planet WHERE system_id = :id");
$stmt->bindValue(':id', $_GET['id']);
$stmt->execute();
$planets = array();
while ($row = $stmt->fetch()) {
  $planets[] = $row;
}

$system = new System($db);
$system->load($_GET['id']);
$header = getOwnershipDetails($system) . " " . $system->getName().
  " System";

?>
<h1><?php echo $header; ?></h1>
<p><?php echo nl2br($result['notes']); ?></p>
<a href="/">Back</a> &bull; 
<?php CRUD::renderEditLink("system", $result['id']); ?> &bull; 
<?php CRUD::renderNewLink("system"); ?>
<?php renderOwnershipLinks($system); ?>
<h2>Orbital Map</h2>
<?php render_orbit_map('system_id', $result['id']); ?>
<h2>Planets</h2>
<ul>
<?php
foreach ($planets as $row) {
?>
<li>
  <a href="/planet.php?id=<?php echo $row['id']; ?>">
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
WHERE planet.system_id = :param OR ship.system_id = :param", $_GET['id']); ?>
<?php list_players($db, "
LEFT JOIN planet ON planet.id = place.planet_id
LEFT JOIN ship ON ship.place_id = place.id
WHERE planet.system_id = :param OR ship.system_id = :param", $_GET['id']); ?>