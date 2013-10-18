<?php
$allow_anonymous = true;

require 'include.php';

if (!$auth->isAnonymous()) {
  header('Location: /overview.php');
  die();
}

if (isset($_POST['submit'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  
  if ($auth->attemptLogin($username, $password)) {
    header('Location: /overview.php');
    die();
  }
}

?>
<h1 style="text-align: center;">Stolen Stars</h1>
<p>Stolen Stars is an online role playing system.  It is currently in private alpha.</p>
<p>Login if you have an account:</p>
<form action="/" method="post">
<table>
  <tr>
    <td>Username:</td>
    <td><input name="username" type="text" /></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td><input name="password" type="password" /></td>
  </tr>
  <tr>
    <td colspan="2"><input type="submit" name="submit" value="Login" /></td>
  </tr>
</table>
</form>