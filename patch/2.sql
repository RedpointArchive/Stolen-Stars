INSERT INTO planet
(name, notes, leadership, system_id, category)
VALUES
('Zazzar IV', 'Drug meeting, has more info about Kamchatka (rumour)', '???', (SELECT id FROM system where name = 'Zazzar'), 'Middle Worlds');