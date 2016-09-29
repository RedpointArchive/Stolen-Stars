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
  'Medic',
  3,
  0,
  0,
  5,
  10,
  10,
  8,
  7,
  'Worked at an experimental facility for the alliance, before being exiled for research into building a ' ${concat}
  'healing device that would cure and heal an entire planet continuously.  Because of the obvious implications ' ${concat}
  'for the alliance''s power in the galaxy, the facility was shut down and almost everyone related to the research ' ${concat}
  'was killed; only a few managed to escape from the alliance and to the outer planets.',
  'He wants to finish his research.',
  'fdgdfg'
);

UPDATE player
SET stats_id = ${lastinsert}
WHERE name = 'Javair Sana';