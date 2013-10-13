<?php
include 'include.php';

$stmt = $db->prepare("
SELECT 
  place.id,
  place.name,
  place.notes,
  place.planet_id,
  ship.id AS ship_id,
  ship.name AS ship_name
FROM place
LEFT JOIN ship ON ship.place_id = place.id
WHERE place.id = :id");
$stmt->bindValue(':id', $_GET['id']);
$results = $stmt->execute();
$result = $results->fetchArray();
if (!$result) die('No such place.');

?>
<h1><?php echo $result['name']; ?></h1>
<?php
if ($result['ship_id'] !== null) {
?><p><em>This place is the ship
'<a href="/ship.php?id=<?php echo $result['ship_id']; ?>"><?php echo $result['ship_name']; ?>
</a>'</em></p><?php
}
?>
<p><?php echo nl2br($result['notes']); ?></p>
<a href="/planet.php?id=<?php echo $result['planet_id']; ?>">Back</a>
<?php list_players($db, "
WHERE player.place_id = :param", $_GET['id']); ?>