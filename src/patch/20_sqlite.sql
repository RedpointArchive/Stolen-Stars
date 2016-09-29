INSERT INTO skill (parent_id, name) VALUES
((SELECT id FROM skill WHERE name = 'Athletics'), 'Running'),
((SELECT id FROM skill WHERE name = 'Athletics'), 'Carrying'),
((SELECT id FROM skill WHERE name = 'Guns'), 'Handguns'),
((SELECT id FROM skill WHERE name = 'Medical'), 'Combat'),
((SELECT id FROM skill WHERE name = 'Medical'), 'Experimental/Research');

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Athletics'),
  6
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Running'),
  12
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Carrying'),
  12
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Guns'),
  6
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Handguns'),
  12
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Medical'),
  6
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Combat'),
  12
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Experimental/Research'),
  12
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Melee'),
  6
);

INSERT INTO stats_skill (stats_id, skill_id, value) VALUES
(
  (SELECT stats_id FROM player WHERE name = 'Javair Sana'),
  (SELECT id FROM skill WHERE name = 'Science'),
  6
);