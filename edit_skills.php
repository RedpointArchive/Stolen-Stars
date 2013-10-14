<?php
include 'include.php';

$stmt = $db->prepare("
SELECT
  skill.id,
  skill.name,
  skill.parent_id
FROM skill
LEFT JOIN skill AS parent_skill
  ON parent_skill.id = skill.parent_id
ORDER BY 
(
  case
    when skill.parent_id is null then skill.name
    else parent_skill.name || ' -> ' || skill.name
  end
) ASC");
$stmt->execute();
$all_skills = array();
while ($row = $stmt->fetch()) {
  $all_skills[] = $row;
}

$stmt = $db->prepare("
SELECT
  skill.id AS skill_id,
  skill.name AS skill_name,
  skill.parent_id AS skill_parent_id,
  stats_skill.id AS id,
  stats_skill.value AS value
FROM stats_skill
LEFT JOIN skill
  ON skill.id = stats_skill.skill_id
LEFT JOIN skill AS parent_skill
  ON parent_skill.id = skill.parent_id
WHERE stats_skill.stats_id = :id
ORDER BY 
(
  case
    when skill.parent_id is null then skill.name
    else parent_skill.name || ' -> ' || skill.name
  end
) ASC");
$stmt->bindValue(':id', $_GET['id']);
$stmt->execute();
$skills = array();
while ($row = $stmt->fetch()) {
  $skills[] = $row;
}

if (array_key_exists('submit', $_POST)) {
  foreach ($_POST as $key => $value) {
    if (substr($key, 0, 9) == 'skilltype') {
      $id = (int)substr($key, 9);
      if ((int)$_POST[$key] === -1) {
        $stmt = $db->prepare("
DELETE FROM stats_skill
WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
      } else {
        $stmt = $db->prepare("
UPDATE stats_skill
SET skill_id = :type_id
WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':type_id', $_POST[$key]);
        $stmt->execute();
      }
    }
    if (substr($key, 0, 8) == 'skillval') {
      $id = (int)substr($key, 8);
      $stmt = $db->prepare("
UPDATE stats_skill
SET value = :value
WHERE id = :id");
      $stmt->bindValue(':id', $id);
      $stmt->bindValue(':value', $_POST[$key]);
      $stmt->execute();
    }
    if ($key == 'newtype') {
      $stmt = $db->prepare("
INSERT INTO stats_skill
(stats_id, skill_id, value)
VALUES
(:stats_id, :skill_id, :value)");
      $stmt->bindValue(':stats_id', $_GET['id']);
      $stmt->bindValue(':skill_id', $_POST['newtype']);
      $stmt->bindValue(':value', $_POST['newval']);
      $stmt->execute();
    }
  }
  create_log($db, "The skills of ".find_related_name($db, $_GET['id'])." were edited");
  
  header('Location: '.find_related_url($db, $_GET['id']));
  die();
}

?>
<h1>Edit Skills</h1>
<a href="<?php echo find_related_url($db, $_GET['id']); ?>">Back / Cancel Changes</a>
<form action="/edit_skills.php?id=<?php echo $_GET['id']; ?>" method="post">
<h2>Skills</h2>
<table>
  <tr>
    <th width="200">Skill</th>
    <th>Value</th>
  </tr>
<?php
  foreach ($skills as $skill) {
?>
  <tr>
    <td><select name="skilltype<?php echo $skill['id']; ?>">
    <option value="-1">(delete this skill)</option>
<?php
    foreach ($all_skills as $skilltype) {
      $selected = '';
      if ($skilltype['id'] == $skill['skill_id']) {
        $selected = ' selected="selected"';
      }
      $name = $skilltype['name'];
      if ($skilltype['parent_id'] !== null) {
        foreach ($all_skills as $specialisation) {
          if ($specialisation['id'] === $skilltype['parent_id']) {
            $name = ''.$specialisation['name'].' -> '.$name;
          }
        }
      }
      echo '<option value="'.$skilltype['id'].'"'.$selected.'>'.$name.'</option>';
    }
?></td>
    <td>
      <input type="number" name="skillval<?php echo $skill['id']; ?>" value="<?php echo $skill['value']; ?>" />
    </td>
  </tr>
<?php
  }
?>
  <tr>
    <td><select name="newtype"><option value="-1">(nothing)</option><?php
    foreach ($all_skills as $skilltype) {
      $name = $skilltype['name'];
      if ($skilltype['parent_id'] !== null) {
        foreach ($all_skills as $specialisation) {
          if ($specialisation['id'] === $skilltype['parent_id']) {
            $name = ''.$specialisation['name'].' -> '.$name;
          }
        }
      }
      echo '<option value="'.$skilltype['id'].'">'.$name.'</option>';
    }
?></td>
    <td>
      <input type="number" name="newval" value="" />
    </td>
  </tr>
</table>
<br/>
<input type="submit" name="submit" value="Save All Changes!" />
</form>