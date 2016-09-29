INSERT INTO place (planet_id, name) VALUES
((SELECT id FROM planet where name = 'Zazzar IV'), 'Drug Meeting Spot');

UPDATE ship
SET place_id = (SELECT id FROM place WHERE name = 'Drug Meeting Spot')
WHERE id = (SELECT id FROM ship WHERE name = 'The Black Arc');