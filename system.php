<?php
include 'include.php';
?>
<h1>Planets</h1>
<a href="/">Back</a>
<ul>
<?php
$stmt = $db->prepare("SELECT * FROM planet WHERE system_id = :id");
$stmt->bindValue(':id', $_GET['id']);
$results = $stmt->execute();
while ($row = $results->fetchArray()) {
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