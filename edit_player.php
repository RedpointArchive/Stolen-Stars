<?php
include 'include.php';

$stmt = $db->prepare("
SELECT 
  player.id,
  player.name,
  player.real_name,
  player.place_id,
  player.stats_id,
  place.name as place_name,
  stats.bio,
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
$results = $stmt->execute();
$result = $results->fetchArray();
if (!$result) die('No such player.');

if ($_POST['submit']) {
  $stmt = $db->prepare("
  UPDATE player
  SET
    name = :name,
    real_name = :real_name
  WHERE id = :id");
  $stmt->bindValue(':id', $result['id']);
  $stmt->bindValue(':name', $_POST['name']);
  $stmt->bindValue(':real_name', $_POST['real_name']);
  $stmt->execute();
  
  if ($result['stats_id'] === null) {
    $stmt = $db->prepare("INSERT INTO stats (bio) VALUES ('');");
    $stmt->execute();
    $stats_id = $db->lastInsertRowID();
  } else {
    $stats_id = $result['stats_id'];
  }
  $stmt = $db->prepare("
  UPDATE stats
  SET
    bio = :bio,
    past = :past,
    goal = :goal,
    plot_points = :plot_points,
    wounds = :wounds,
    stun = :stun,
    strength = :strength,
    agility = :agility,
    intelligence = :intelligence,
    willpower = :willpower,
    alertness = :alertness
  WHERE id = :id");
  $stmt->bindValue(':id', $stats_id);
  $stmt->bindValue(':bio', $_POST['bio']);
  $stmt->bindValue(':past', $_POST['past']);
  $stmt->bindValue(':goal', $_POST['goal']);
  $stmt->bindValue(':plot_points', $_POST['plot_points']);
  $stmt->bindValue(':wounds', $_POST['wounds']);
  $stmt->bindValue(':stun', $_POST['stun']);
  $stmt->bindValue(':strength', $_POST['strength']);
  $stmt->bindValue(':agility', $_POST['agility']);
  $stmt->bindValue(':intelligence', $_POST['intelligence']);
  $stmt->bindValue(':willpower', $_POST['willpower']);
  $stmt->bindValue(':alertness', $_POST['alertness']);
  $stmt->execute();
  
  create_log($db, $result['name'].' was edited');
  
  header('Location: /player.php?id='.$_GET['id']);
  die();
}

?>
<h1><?php echo $result['name'] ?> (played by <?php echo $result['real_name'] ?>)</h1>
<a href="/player.php?id=<?php echo $result['id']; ?>">Back / Cancel Changes</a>
<form action="/edit_player.php?id=<?php echo $result['id']; ?>" method="post">
<h2>Stats</h2>
<table>
  <tr>
    <th width="200">Key</th>
    <th>Value</th>
  </tr>
  <tr>
    <td>Character Name</td>
    <td><input name="name" value="<?php echo $result['name']; ?>" /></td>
  </tr>
  <tr>
    <td>Person Name</td>
    <td><input name="real_name" value="<?php echo $result['real_name']; ?>" /></td>
  </tr>
  <tr>
    <td>Plot Points</td>
    <td><input name="plot_points" value="<?php echo $result['plot_points']; ?>" type="number" /></td>
  </tr>
  <tr>
    <td>Life Points</td>
    <td>(calculated field)</td>
  </tr>
  <tr>
    <td>Wounds</td>
    <td><input name="wounds" value="<?php echo $result['wounds']; ?>" type="number" /></td>
  </tr>
  <tr>
    <td>Stun</td>
    <td><input name="stun" value="<?php echo $result['stun']; ?>" type="number" /></td>
  </tr>
  <tr>
    <td>Initiative</td>
    <td>(calculated field)</td>
  </tr>
  <tr>
    <td>Strength</td>
    <td><input name="strength" value="<?php echo $result['strength']; ?>" type="number" /></td>
  </tr>
  <tr>
    <td>Agility</td>
    <td><input name="agility" value="<?php echo $result['agility']; ?>" type="number" /></td>
  </tr>
  <tr>
    <td>Intelligence</td>
    <td><input name="intelligence" value="<?php echo $result['intelligence']; ?>" type="number" /></td>
  </tr>
  <tr>
    <td>Willpower</td>
    <td><input name="willpower" value="<?php echo $result['willpower']; ?>" type="number" /></td>
  </tr>
  <tr>
    <td>Alertness</td>
    <td><input name="alertness" value="<?php echo $result['alertness']; ?>" type="number" /></td>
  </tr>
</table>
<h2>Biography</h2>
<textarea name="bio" style="width: 80%;" rows="20">
<?php echo $result['bio']; ?>
</textarea>
<h2>Past Events</h2>
<textarea name="past" style="width: 80%;" rows="20">
<?php echo $result['past']; ?>
</textarea>
<h2>Future Goal</h2>
<textarea name="goal" style="width: 80%;" rows="20">
<?php echo $result['goal']; ?>
</textarea>
<h2>SAVE CHANGES</h2>
<input type="submit" name="submit" value="Save All Changes!" />
</form>