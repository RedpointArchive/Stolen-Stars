<?php
$allow_anonymous = true;

// TODO: Finish authorization logic.
header('Location: /overview.php');
die();

include 'include.php';
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