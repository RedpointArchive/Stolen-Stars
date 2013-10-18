<?php
require '../include.php';

$user = new User($db);
$users = $user->loadAll();

?>
<h1>Manage Users</h1>
<a href="/admin/">Back</a>
<h2>User List</h2>
<table>
  <tr>
    <th>Username</th>
    <th>Email</th>
    <th>Actions</th>
  </tr>
<?php
foreach ($users as $user) {
?>
  <tr>
    <td><?php echo $user->getUsername(); ?></td>
    <td><?php echo $user->getEmail(); ?></td>
    <td><a href="/admin/edit_user.php?id=<?php echo $user->getID(); ?>&amp;r=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Edit</a></td>
  </tr>
<?php
}
?>
</table>
<br />
<form action="/admin/users.php?mode=new" method="post">
  <input type="submit" name="submit" value="Create User" />
</form>
