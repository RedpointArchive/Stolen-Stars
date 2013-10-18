<?php
require 'include.php';

if (isset($_GET['mode'])) {
  switch ($_GET['mode']) {
    case 'email':
      $auth->getUser()->setEmail($_POST['email']);
      $auth->getUser()->save();
      $_SESSION['success'] = 'Your email has been changed.';
      header('Location: /account.php');
      die();
      break;
    case 'password':
      if (!$auth->getUser()->checkPassword($_POST['current_password'])) {
        $_SESSION['error'] = 'Current password is not correct.';
      } else if ($_POST['new_password'] == $_POST['confirm_password']) {
        $auth->getUser()->setPassword($_POST['new_password']);
        $auth->getUser()->save();
        $_SESSION['success'] = 'Your password has been changed.';
      } else {
        $_SESSION['error'] = 'Passwords do not match.';
      }
      header('Location: /account.php');
      die();
      break;
    default:
      die('Invalid action.');
      break;
  }
}
?>
<h1>Your Account</h1>
<a href="/">Back</a>
<h2>Change Email</h2>
<form action="/account.php?mode=email" method="POST">
  <table>
    <tr>
      <td>Current Email:</td>
      <td><?php echo $auth->getUser()->getEmail(); ?></td>
    </tr>
    <tr>
      <td>New Email:</td>
      <td><input type="text" name="email" /></td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="submit" name="submit" value="Submit" />
      </td>
    </tr>
  </table>
</form>
<h2>Change Password</h2>
<form action="/account.php?mode=password" method="POST">
  <table>
    <tr>
      <td>Current Password:</td>
      <td><input type="password" name="current_password" /></td>
    </tr>
    <tr>
      <td>New Password:</td>
      <td><input type="password" name="new_password" /></td>
    </tr>
    <tr>
      <td>Confirm Password:</td>
      <td><input type="password" name="confirm_password" /></td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="submit" name="submit" value="Submit" />
      </td>
    </tr>
  </table>
</form>