CREATE TABLE system
(
  id INTEGER PRIMARY KEY ${autoincrement},
  name TEXT
);

CREATE TABLE planet
(
  id INTEGER PRIMARY KEY ${autoincrement},
  name TEXT,
  notes TEXT,
  leadership TEXT,
  system_id INTEGER,
  category TEXT
);

CREATE TABLE place
(
  id INTEGER PRIMARY KEY ${autoincrement},
  planet_id INTEGER,
  name TEXT
);

CREATE TABLE ship
(
  id INTEGER PRIMARY KEY ${autoincrement},
  place_id INTEGER,
  system_id INTEGER,
  name TEXT
);

CREATE TABLE player
(
  id INTEGER PRIMARY KEY ${autoincrement},
  party_id INTEGER,
  place_id INTEGER,
  name TEXT,
  real_name TEXT
);

CREATE TABLE party
(
  id INTEGER PRIMARY KEY ${autoincrement},
  ship_id INTEGER,
  name TEXT
);

CREATE TABLE info
(
  version INTEGER
);

INSERT INTO info (version) VALUES (0);