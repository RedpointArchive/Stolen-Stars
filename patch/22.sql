CREATE TABLE inventory
(
  id INTEGER PRIMARY KEY ${autoincrement}
);

CREATE TABLE store_template
(
  id INTEGER PRIMARY KEY ${autoincrement},
  name TEXT,
  inventory_template_id INTEGER
);

CREATE TABLE store
(
  id INTEGER PRIMARY KEY ${autoincrement},
  store_template_id INTEGER,
  place_id INTEGER,
  inventory_id INTEGER,
  name TEXT
);

ALTER TABLE player
ADD inventory_id INTEGER;

CREATE TABLE item
(
  id INTEGER PRIMARY KEY ${autoincrement},
  name TEXT,
  has_quantity BOOLEAN
);

CREATE TABLE attribute
(
  id INTEGER PRIMARY KEY ${autoincrement},
  name TEXT
);

CREATE TABLE inventory_item
(
  id INTEGER PRIMARY KEY ${autoincrement},
  inventory_id INTEGER,
  item_id INTEGER,
  quantity INTEGER,
  value TEXT
);

CREATE TABLE inventory_item_attribute
(
  id INTEGER PRIMARY KEY ${autoincrement},
  inventory_item_id INTEGER,
  attribute_id INTEGER,
  value TEXT
);