<?php

require 'dba/sqlite.php';
require 'dba/mysql.php';

// Configure Stolen Stars to use either SQLite or MySQL.
function ss_pdo_connect() {
  return ss_sqlite_connect();
}
function ss_pdo_transform($sql) {
  return ss_sqlite_transform($sql);
}
function ss_pdo_name() {
  return ss_sqlite_name();
}