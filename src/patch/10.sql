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
  'Scout Sniper',
  5,
  0,
  0,
  6,
  12,
  6,
  6,
  10,
  'Grayson resigned from the Alliance Military after a mission he was on went...badly.',
  'Get by day to day and earn his keep. Slowly put the past behind him. Move on.',
  'After showing promise as a small boy, Grayson was conscripted into service with his local Alliance military branch. He showed exceptional skill as a Scout Sniper and was heavily trained in this field.
 
A few years later Grayson resigned from service after a classified mission went...badly. The details are a mystery.
 
Now he hopes to get on with his life, using his skills as a Scout Sniper to earn his keep aboard Merc vessels and the like.'
);

UPDATE player
SET stats_id = ${lastinsert}
WHERE name = 'Grayson Maines';