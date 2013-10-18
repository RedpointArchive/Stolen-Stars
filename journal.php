<?php
require 'include.php';

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
$stmt->execute();
$result = $stmt->fetch();
if (!$result) die('No such player.');

if (array_key_exists('submit', $_POST)) {
  $stmt = $db->prepare("
  INSERT INTO journal
  (player_id, created, content)
  VALUES
  (:id, :created, :content)");
  $stmt->bindValue(':id', $_GET['id']);
  $stmt->bindValue(':created', time());
  $stmt->bindValue(':content', $_POST['entry']);
  $stmt->execute();
  
  header('Location: /player.php?id='.$_GET['id']);
  die();
}

?>
<h1>New journal for <?php echo $result['name']; ?></h1>
<form action="/journal.php?id=<?php echo $_GET['id']; ?>" method="post">
<textarea name="entry" cols="50" rows="20">
</textarea><br/>
<input name="submit" type="submit" value="Create" />
</form>