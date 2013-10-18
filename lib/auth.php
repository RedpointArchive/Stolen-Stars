<?php

final class User extends DAO {
  protected $username;
  protected $password;
  protected $email;
  protected $application;
  protected $approved;
  protected $is_administrator;
  
  public function checkPassword($input) {
    return sha1('!@#$ss4'.$input) == $this->password;
  }
  
  public function setPassword($input) {
    $this->password = sha1('!@#$ss4'.$input);
  }
  
  public function attemptLogin($username, $password) {
    $results = $this->loadAllWhere(
      "username = :username AND password = :password",
      array(
        'username' => $username,
        'password' => sha1('!@#$ss4'.$password)));
    if (count($results) === 0) {
      return false;
    }
    return $results[0];
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
  private $session;
  
  const ERROR_LOGIN_INCORRECT = 'incorrect';
  const ERROR_NOT_LOGGED_IN = 'notloggedin';
  const ERROR_NOT_APPROVED = 'approved';
  const ERROR_SESSION_CORRUPT = 'corrupt';
  const ERROR_SESSION_EXPIRED = 'expired';
  const SUCCESS = 'success';

  public function __construct($db) {
    session_start();
    $this->db = $db;
    $this->loginURL = "/";
    $this->user = null;
    $this->session;
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
  
  public function handleFailure($error) {
    switch ($error) {
      case self::ERROR_LOGIN_INCORRECT:
        $message = 'The username / password is incorrect.';
        break;
      case self::ERROR_NOT_APPROVED:
        $message = 'Your user account has not yet been approved.';
        break;
      case self::ERROR_NOT_LOGGED_IN:
        $message = 'You are not logged in!';
        break;
      case self::ERROR_SESSION_CORRUPT:
        $message = 'Your session is corrupt.';
        break;
      case self::ERROR_SESSION_EXPIRED:
        $message = 'Your session has expired.  Please login again.';
        break;
    }
    $_SESSION['error'] = $message;
    $this->redirectToLogin();
  }
  
  public function attemptLogin($username, $password) {
    $user = new User($this->db);
    $result = $user->attemptLogin($username, $password);
    if ($result === false) {
      return self::ERROR_LOGIN_INCORRECT;
    }
    
    if (!$result->getApproved()) {
      return self::ERROR_NOT_APPROVED;
    }
    
    // Set up the session.
    $token = sha1(time() + $result->getUsername());
    $session = new Session($this->db);
    $session->setUserID($result->getID());
    $session->setExpiry(time() + 6 * 60 * 60);
    $session->setSessionToken($token);
    $session->save();
    
    // Set the cookie.
    setcookie("session", $token);
    
    return true;
  }
  
  public function logout() {
    $this->session->delete();
  }

  public function authorize() {
    if (!isset($_COOKIE['session'])) {
      return self::ERROR_NOT_LOGGED_IN;
    }
    
    // Find the session associated with the cookie.
    $session = new Session($this->db);
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
      return self::ERROR_SESSION_CORRUPT;
    }
    
    // If there's no session, redirect to login.
    if (count($sessions) == 0) {
      return self::ERROR_NOT_LOGGED_IN;
    }
    
    // If there's one session, check it's expiry.
    $session = $sessions[0];
    if ($session->getExpiry() < time()) {
      $session->delete();
      return self::ERROR_SESSION_EXPIRED;
    }
    
    // Otherwise the session is valid.  Update the expiry
    // to last for another 6 hours.
    $session->setExpiry(time() + 6 * 60 * 60);
    $session->save();
    
    // Load the user information.
    $user = new User($this->db);
    $user->load($session->getUserID());
    
    // Ensure the user is approved.
    if (!$user->getApproved()) {
      $session->delete();
      return self::ERROR_NOT_APPROVED;
    }
    
    $this->user = $user;
    $this->session = $session;
    return self::SUCCESS;
  }

}