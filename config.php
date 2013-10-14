<?php

// Connection for SQLite.
function ss_sqlite_connect() {
  if (!file_exists("db.sqlite3")) {
    $db = new SQLite3("db.sqlite3");
    $db->exec(file_get_contents("init.sql"));
    $db->close();
  }

  $db = new PDO(
    "sqlite:db.sqlite3",
    "",
    "",
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  return $db;
}

// Connection for MySQL.
function ss_mysql_connect() {
  $db = new PDO(
    "mysql:host=localhost",
    "root",
    "",
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  
  // Check the DB exists, if it doesn't then we
  // need to do the init.
  if ($db->query("USE stolenstars;") === false) {
    $db->query("CREATE stolenstars");
    $db->query("USE stolenstars");
    $db->exec(file_get_contents("init.sql"));
  }
  
  return $db;
}

// Configure Stolen Stars to use either SQLite or MySQL.
function ss_pdo_connect() {
  return ss_sqlite_connect();
}