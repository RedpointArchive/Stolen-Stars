<?php
require 'include.php';
$stmt = $db->prepare("
SELECT 
  player.id,
  player.name,
  player.real_name,
  player.place_id,
  player.stats_id,
  place.name as place_name,
  stats.bio,
  stats.inventory,
  stats.past,
  stats.goal,
  stats.plot_points,
  stats.wounds,
  stats.stun,
  stats.strength,
  stats.agility,
  stats.intelligence,
  stats.willpower,
  stats.alertness
FROM player
LEFT JOIN stats ON player.stats_id = stats.id
LEFT JOIN place ON place.id = player.place_id
WHERE player.id = :id");
$stmt->bindValue(':id', $_GET['id']);
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such player.');
?>
<h1><?php echo $result['name'] ?> (played by <?php echo $result['real_name'] ?>)</h1>
<p>
  <strong>Plot Points:</strong> <?php echo $result['plot_points']; ?><br/>
  <br/>
  <strong>Life Points:</strong> <?php echo player_get_life_points($result); ?><br/>
  <strong>Wounds:</strong> <?php echo $result['wounds']; ?><br/>
  <strong>Stun:</strong> <?php echo $result['stun']; ?><br/>
  <strong>Initiative:</strong> <?php echo player_get_initiative($result); ?><br/>
  <br/>
  <strong>Strength:</strong> <?php echo $result['strength']; ?><br/>
  <strong>Agility:</strong> <?php echo $result['agility']; ?><br/>
  <strong>Intelligence:</strong> <?php echo $result['intelligence']; ?><br/>
  <strong>Willpower:</strong> <?php echo $result['willpower']; ?><br/>
  <strong>Alertness:</strong> <?php echo $result['alertness']; ?>
</p>
<a href="/">Back</a> &bull; 
<a href="/edit_player.php?id=<?php echo $result['id']; ?>">Edit</a>
<h2>Skills</h2>
<?php list_skills($db, $result["stats_id"]); ?>
<a href="/edit_skills.php?id=<?php echo $result['stats_id']; ?>">Edit</a>
<h2>Biography</h2>
<p><?php echo nl2br($result['bio']); ?></p>
<h2>Past Events</h2>
<p><?php echo nl2br($result['past']); ?></p>
<h2>Future Goal</h2>
<p><?php echo nl2br($result['goal']); ?></p>
<h2>Location</h2>
<p>Currently at <a href="/place.php?id=<?php echo $result['place_id']; ?>">
<?php echo $result['place_name']; ?></a></p>
<form action="/move_player.php?id=<?php echo $_GET['id']; ?>" method="POST">
  <p>Move to: <select name="target">
  <?php
    $places = get_grouped_places($db);
    foreach ($places as $key => $rows) {
      if ($key != null) {
        echo '<optgroup label="'.$key.'">';
      }
      foreach ($rows as $row) {
        $selected = "";
        if ($row['id'] == $result['place_id']) {
          $selected = 'selected="selected"';
        }
        echo '<option value="'.$row['id'].'"'.$selected.'>'.$row['name'].'</option>';
      }
      if ($key != null) {
        echo '</optgroup>';
      }
    }
  ?>
  </select> <input type="submit" /></p>
</form>
<h2>Inventory</h2>
<?php
$inv = Inventory::loadFromPlayer($db, $_GET['id']);
$inv->render();
?>
<a href="/edit_inventory.php?player_id=<?php echo $_GET['id']; ?>">Edit</a>
<?php if (trim($result['inventory']) != "") { ?>
<h3>Additional Inventory</h3>
<?php } ?>
<p><?php echo nl2br($result['inventory']); ?></p>
<h2>Journal Entries</h2>
<?php
$stmt = $db->prepare("SELECT * FROM journal WHERE player_id = :id ORDER BY created DESC");
$stmt->bindValue(":id", $_GET['id']);
$stmt->execute();
$count = 0;
while ($row = $stmt->fetch()) {
  echo '<h3>'.date(DATE_RFC822, $row['created']).' (';
  CRUD::renderEditLink("journal", $row['id']);
  echo ')</h3>';
  echo '<p>'.$row['content'].'</p>';
  $count++;
}
if ($count == 0) {
  echo 'This character has no journal entries.';
}
?>
<p><?php CRUD::renderNewLinkWithText("Create new journal entry", "journal", array("player_id" => $_GET['id'])); ?></p>