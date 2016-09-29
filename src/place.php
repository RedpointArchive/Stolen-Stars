<?php
require 'include.php';

$stmt = $db->prepare("
SELECT 
  place.id,
  place.name,
  place.notes,
  place.planet_id,
  ship.id AS ship_id,
  ship.system_id AS system_id,
  ship.name AS ship_name
FROM place
LEFT JOIN ship ON ship.place_id = place.id
WHERE place.id = :id");
$stmt->bindValue(':id', $_GET['id']);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such place.');

$place = new Place($db);
$place->load($_GET['id']);
$header = getOwnershipDetails($place) . " " . $place->getName();

?>
<h1><?php echo $header; ?></h1>
<?php
if ($result['ship_id'] !== null) {
?><p><em>This place is the ship
'<a href="/ship.php?id=<?php echo $result['ship_id']; ?>"><?php echo $result['ship_name']; ?>
</a>'</em></p><?php
}
?>
<p><?php echo nl2br($result['notes']); ?></p>
<?php
if ($result['planet_id'] !== null) {
?>
<a href="/planet.php?id=<?php echo $result['planet_id']; ?>">Back</a>
<?php
} else {
?>
<a href="/system.php?id=<?php echo $result['system_id']; ?>">Back</a>
<?php
}
?> &bull; 
<?php CRUD::renderEditLink("place", $result['id']); ?> &bull; 
<?php CRUD::renderNewLink("place"); ?>
<?php renderOwnershipLinks($place); ?>
<?php list_players($db, "
WHERE player.place_id = :param", $_GET['id']); ?>