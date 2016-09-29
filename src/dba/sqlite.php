<?php

// Connection for SQLite.
function ss_sqlite_connect() {
  if (!file_exists("db.sqlite3")) {
    $db = new SQLite3("db.sqlite3");
    $db->exec(
      ss_pdo_transform(
        file_get_contents("init.sql")));
    $db->close();
  }

  $db = new PDO(
    "sqlite:db.sqlite3",
    "",
    "",
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  return $db;
}

// SQL transform for SQLite.
function ss_sqlite_transform($sql) {
  return str_replace(
    '${autoincrement}',
    '',
    str_replace(
      '${lastinsert}',
      'last_insert_rowid()',
      str_replace(
        '${concat}',
        '||',
        $sql)));
}

// SQL name for patches for SQLite.
function ss_sqlite_name() {
  return "sqlite";
}
