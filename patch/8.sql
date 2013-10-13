CREATE TABLE stats
(
  id INTEGER PRIMARY KEY,
  role TEXT,
  plot_points INTEGER,
  wounds INTEGER,
  stun INTEGER,
  strength INTEGER,
  agility INTEGER,
  intelligence INTEGER,
  willpower INTEGER,
  alertness INTEGER,
  past TEXT,
  goal TEXT,
  bio TEXT
);

ALTER TABLE player
ADD stats_id INTEGER;