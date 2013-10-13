CREATE TABLE skill
(
  id INTEGER PRIMARY KEY,
  parent_id INTEGER,
  name TEXT
);

CREATE TABLE stats_skill
(
  id INTEGER PRIMARY KEY,
  stats_id INTEGER,
  skill_id INTEGER,
  value INTEGER
);

INSERT INTO skill (parent_id, name) VALUES
(null, 'Athletics'),
(null, 'Covert'),
(null, 'Electronics'),
(null, 'Guns'),
(null, 'History'),
(null, 'Influence'),
(null, 'Mechanical'),
(null, 'Medical'),
(null, 'Melee'),
(null, 'Pilot'),
(null, 'Perception'),
(null, 'Science');