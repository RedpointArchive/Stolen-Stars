UPDATE ship
SET 
  place_id = (SELECT id FROM place WHERE name = 'The Black Arc'),
  system_id = NULL
WHERE name = 'The Black Arc';

UPDATE place
SET planet_id = (SELECT id FROM planet WHERE name = 'Zazzar IV')
WHERE name = 'The Black Arc';