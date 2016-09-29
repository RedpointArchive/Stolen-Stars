INSERT INTO stats
(
  role,
  plot_points,
  wounds,
  stun,
  strength,
  agility,
  intelligence,
  willpower,
  alertness,
  past,
  goal,
  bio
)
VALUES
(
  'Covert / Infiltrator',
  3,
  0,
  0,
  8,
  12,
  6,
  6,
  8,
  'Trained at a Military facility aimed at cloning covert Infitrators. Each clone is assigned a target  to complete training.',
  'In search of someone called Dark Oracle to gain information in finding his target Antonio Maffei',
  'N/A'
);

UPDATE player
SET stats_id = ${lastinsert}
WHERE name = 'Cody';