<?php
require '../include.php';

$skill = new Skill($db);
$skills = $skill->loadAll();

?>
<h1>Manage Skills</h1>
<a href="/admin/">Back</a>
<h2>Skill List</h2>
<table>
  <tr>
    <th>Name</th>
    <th>Parent</th>
    <th>Actions</th>
  </tr>
<?php
foreach ($skills as $skill) {
  $p = $skill->getParentID();
  if ($p === null) {
    $p = "(none)";
  } else {
    $p = $skill->getParent()->getName();
  }
?>
  <tr>
    <td><?php echo $skill->getName(); ?></td>
    <td><?php echo $p; ?></td>
    <td><a href="/admin/edit_skill.php?id=<?php echo $skill->getID(); ?>&amp;r=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Edit</a></td>
  </tr>
<?php
}
?>
</table>
<br />
<a href="/admin/edit_skill.php?id=-1&amp;r=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">
  Create Skill
</a>