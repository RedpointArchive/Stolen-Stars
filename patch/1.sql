INSERT INTO system (name)
VALUES ('Arro'), ('Zazzar');

INSERT INTO planet
(name, notes, leadership, system_id, category)
VALUES
('Wocarro', 'Has a small settlement near a cave.', '???', (SELECT id FROM system where name = 'Arro'), 'Middle Worlds'),
('Wocarro''s Moon', 'Has a Space Bar and recreational facilities.', '???', (SELECT id FROM system where name = 'Arro'), 'Middle Worlds');

INSERT INTO place
(planet_id, name)
VALUES (null, 'The Black Arc');

INSERT INTO ship
(name, place_id, system_id)
VALUES (
  'The Black Arc',
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  (SELECT id FROM system WHERE name = 'Zazzar')
);

INSERT INTO party
(name, ship_id)
VALUES ('Stolen Stars Group', (SELECT id FROM ship WHERE name = 'The Black Arc'));

INSERT INTO player
(party_id, place_id, name, real_name)
VALUES (
  (SELECT id FROM party WHERE name = 'Stolen Stars Group'),
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  'Javair Sana',
  'James'
),
(
  (SELECT id FROM party WHERE name = 'Stolen Stars Group'),
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  'Gaheris Tesla',
  'Josh'
),
(
  (SELECT id FROM party WHERE name = 'Stolen Stars Group'),
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  'Sergei Abramovich',
  'Pete'
),
(
  (SELECT id FROM party WHERE name = 'Stolen Stars Group'),
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  'k1',
  'Clem'
),
(
  (SELECT id FROM party WHERE name = 'Stolen Stars Group'),
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  'Phillip Stuen',
  'Tom'
),
(
  (SELECT id FROM party WHERE name = 'Stolen Stars Group'),
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  'Cody',
  'Ben'
),
(
  (SELECT id FROM party WHERE name = 'Stolen Stars Group'),
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  'Grayson Maines',
  'Jack'
),
(
  (SELECT id FROM party WHERE name = 'Stolen Stars Group'),
  (SELECT id FROM place WHERE name = 'The Black Arc'),
  'Sabbatine Holtz',
  'Eslin'
);

INSERT INTO place (planet_id, name) VALUES
((SELECT id FROM planet where name = 'Wocarro'), 'Settlement'),
((SELECT id FROM planet where name = 'Wocarro'), 'Caves'),
((SELECT id FROM planet where name = 'Wocarro''s Moon'), 'Space Bar'),
((SELECT id FROM planet where name = 'Wocarro''s Moon'), 'Recreation Room');