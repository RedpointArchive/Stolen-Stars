<?php

final class User extends DAO {
  protected $username;
  protected $password;
  protected $email;
  protected $application;
  protected $approved;
  
  public function checkPassword($input) {
    return sha1('!@#$ss4'.$input) == $password;
  }
  
  public function setPasswordByInput($input) {
    $this->setPassword('!@#$ss4'.$input);
  }
}

final class Session extends DAO {
  protected $user_id;
  protected $session_token;
  protected $expiry;
}

final class Auth {
  private $db;
  private $loginURL;
  private $user;

  public function __construct($db) {
    session_start();
    $this->db = $db;
    $this->loginURL = "/";
    $this->user = null;
  }
  
  public function setLoginURL($url) {
    $this->loginURL = $url;
  }
  
  public function redirectToLogin() {
    header('Location: '.$this->loginURL);
    die();
  }
  
  public function isAnonymous() {
    return $this->user == null;
  }
  
  public function getUser() {
    return $this->user;
  }

  public function authorize() {
    if (!isset($_COOKIE['session'])) {
      $_SESSION['error'] = 'You are not logged in!';
      $this->redirectToLogin();
    }
    
    // Find the session associated with the cookie.
    $session = new Session();
    $sessions = $session->loadAllWhere(
      'session_token = :token',
      array(
        ':token' => $_COOKIE['session']));
    
    // If there's more than one session entry, delete them
    // all and try again.
    if (count($sessions) > 1) {
      foreach ($sessions as $session) {
        $session->delete();
      }
      $_SESSION['error'] = 'Your session is corrupt.';
      $this->redirectToLogin();
    }
    
    // If there's no session, redirect to login.
    if (count($sessions) == 0) {
      $_SESSION['error'] = 'You are not logged in!';
      $this->redirectToLogin();
    }
    
    // If there's one session, check it's expiry.
    $session = $sessions[0];
    if ($session->getExpiry() > time()) {
      $_SESSION['error'] = 'Your session has expired.  Please login again.';
      $session->delete();
      $this->redirectToLogin();
    }
    
    // Otherwise the session is valid.  Update the expiry
    // to last for another 6 hours.
    $session->setExpiry(time() + 6 * 60 * 60);
    $session->save();
    
    // Load the user information.
    $user = new User();
    $user->load($session->getUserID());
    
    // Ensure the user is approved.
    if (!$user->getApproved()) {
      $_SESSION['error'] = 'Your user account has not yet been approved.';
      $session->delete();
      $this->redirectToLogin();
    }
  }

}