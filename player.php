<?php
include 'include.php';
$stmt = $db->prepare("
SELECT 
  player.id,
  player.name,
  player.real_name,
  player.place_id,
  place.name as place_name
FROM player
LEFT JOIN place ON place.id = player.place_id
WHERE player.id = :id");
$stmt->bindValue(':id', $_GET['id']);
$results = $stmt->execute();
$result = $results->fetchArray();
if (!$result) die('No such player.');
?>
<h1><?php echo $result['name'] ?></h1>
<a href="/">Back</a>
<p>Played by <?php echo $result['real_name'] ?></p>
<h1>Location</h1>
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
<h1>Journal Entries</h1>
<?php
$stmt = $db->prepare("SELECT * FROM journal WHERE player_id = :id ORDER BY created DESC");
$stmt->bindValue(":id", $_GET['id']);
$results = $stmt->execute();
$count = 0;
while ($row = $results->fetchArray()) {
  echo '<h2>'.date(DATE_RFC822, $row['created']).'</h2>';
  echo '<p>'.$row['content'].'</p>';
  $count++;
}
if ($count == 0) {
  echo 'This character has no journal entries.';
}
?>
<p><a href="/journal.php?id=<?php echo $_GET['id']; ?>">Create new journal entry</a></p>