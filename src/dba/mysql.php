<?php

// Connection for MySQL.
function ss_mysql_connect() {
  $db = new PDO(
    "mysql:host=localhost",
    "root",
    "",
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $db->query("SET SESSION sql_mode = 'ANSI,TRADITIONAL'");
  
  // Check the DB exists, if it doesn't then we
  // need to do the init.
  try {
    $db->query("USE stolenstars");
  }
  catch (PDOException $ex) {
    try {
      $db->query("CREATE DATABASE stolenstars");
      $db->query("USE stolenstars");
      $db->exec(
        ss_pdo_transform(
          file_get_contents("init.sql")));
    } catch (PDOException $dex) {
      $db->query("DROP DATABASE stolenstars");
      throw $dex;
    } 
  }
  
  return $db;
}

// SQL transform for MySQL.
function ss_mysql_transform($sql) {
  return str_replace(
    '${autoincrement}',
    'AUTO_INCREMENT',
    str_replace(
      '${lastinsert}',
      'LAST_INSERT_ID()',
      str_replace(
        '${concat}',
        '||',
        $sql)));
}

// SQL name for patches for MySQL.
function ss_mysql_name() {
  return "mysql";
}
