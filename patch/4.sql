UPDATE ship
SET place_id = (SELECT id FROM place WHERE name = 'The Black Arc')
WHERE id = (SELECT id FROM ship WHERE name = 'The Black Arc');

UPDATE ship
SET system_id = null
WHERE id = (SELECT id FROM ship WHERE name = 'The Black Arc');

UPDATE place
SET planet_id = (SELECT id FROM planet WHERE name = 'Zazzar IV')
WHERE id = (SELECT id FROM place WHERE name = 'The Black Arc');