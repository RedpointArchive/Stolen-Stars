CREATE TABLE system
(
  id INTEGER PRIMARY KEY,
  name VARCHAR
);

CREATE TABLE planet
(
  id INTEGER PRIMARY KEY,
  name VARCHAR,
  notes VARCHAR,
  leadership VARCHAR,
  system_id INTEGER,
  category INTEGER
);

CREATE TABLE place
(
  id INTEGER PRIMARY KEY,
  planet_id INTEGER,
  name VARCHAR
);

CREATE TABLE ship
(
  id INTEGER PRIMARY KEY,
  place_id INTEGER,
  system_id INTEGER,
  name VARCHAR
);

CREATE TABLE player
(
  id INTEGER PRIMARY KEY,
  party_id INTEGER,
  place_id INTEGER,
  name VARCHAR,
  real_name VARCHAR
);

CREATE TABLE party
(
  id INTEGER PRIMARY KEY,
  ship_id INTEGER,
  name VARCHAR
);

CREATE TABLE info
(
  version INTEGER
);

INSERT INTO info (version) VALUES (0);