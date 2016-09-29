CREATE TABLE user
(
  id INTEGER PRIMARY KEY ${autoincrement},
  username TEXT,
  password TEXT,
  email TEXT,
  application TEXT,
  approved INTEGER
);

CREATE TABLE session
(
  id INTEGER PRIMARY KEY ${autoincrement},
  user_id INTEGER,
  session_token TEXT,
  expiry INTEGER
);