<?php
include 'include.php';
?>
<h1>Systems</h1>
<ul>
<?php
$results = $db->query("SELECT * FROM system");
while ($row = $results->fetchArray()) {
?>
<li>
  <a href="/system.php?id=<?php echo $row['id']; ?>">
    <?php echo $row["name"]; ?>
  </a>
</li>
<?php
}
?>
</ul>
<?php list_ships($db); ?>
<?php list_players($db); ?>