-- Insert subskills.
CREATE TEMPORARY TABLE _skills_to_insert
(
  name TEXT,
  new_name TEXT,
  parent_id INTEGER
);

INSERT INTO _skills_to_insert (name, new_name) VALUES
('Athletics', 'Running'),
('Athletics', 'Carrying'),
('Guns', 'Handguns'),
('Medical', 'Combat'),
('Medical', 'Experimental/Research');

UPDATE _skills_to_insert
INNER JOIN skill
  ON skill.name = _skills_to_insert.name
SET _skills_to_insert.parent_id = skill.id;

INSERT INTO skill (parent_id, name)
SELECT parent_id, new_name FROM _skills_to_insert;

DROP TABLE _skills_to_insert;

-- Insert skills into Javair Sana.
CREATE TEMPORARY TABLE _stats_skill_to_insert
(
  player_name TEXT,
  skill_name TEXT,
  value INTEGER,
  stats_id INTEGER,
  skill_id INTEGER
);

INSERT INTO _stats_skill_to_insert
(player_name, skill_name, value)
VALUES
('Javair Sana', 'Athletics', 6),
('Javair Sana', 'Running', 12),
('Javair Sana', 'Carrying', 12),
('Javair Sana', 'Guns', 6),
('Javair Sana', 'Handguns', 12),
('Javair Sana', 'Medical', 6),
('Javair Sana', 'Combat', 12),
('Javair Sana', 'Experimental/Research', 12),
('Javair Sana', 'Melee', 6),
('Javair Sana', 'Science', 6);

UPDATE _stats_skill_to_insert
INNER JOIN player
  ON player.name = _stats_skill_to_insert.player_name
INNER JOIN stats
  ON stats.id = player.id
INNER JOIN skill
  ON skill.name = _stats_skill_to_insert.skill_name
SET
  _stats_skill_to_insert.stats_id = stats.id,
  _stats_skill_to_insert.skill_id = skill.id;

INSERT INTO stats_skill (stats_id, skill_id, value)
SELECT stats_id, skill_id, value FROM _stats_skill_to_insert;

DROP TABLE _stats_skill_to_insert;
