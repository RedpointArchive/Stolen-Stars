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
  'Pilot',
  6,
  0,
  0,
  5,
  8,
  9,
  7,
  11,
  'Phillip was in the Alliance military, but objected to the coming war and left (or chickened out, depending on who you ask).',
  'Phillip wants to retire, but preferably with enough cash to live well for the rest of his life.',
  'Phillip Stuen was a 13 year veteran pilot in the Alliance military when he saw the pretty clear signs that war with the outer planets was coming.
 
Due to a combination of cowardice (he won''t admit it often) and a sense that the coming war was very wrong (a claim he still clings to) he quit the Alliance military before the war began, which angered some of his comrades (and the Alliance government). During the war he made himself scarce - a couple of attempts were made to recall him to duty, but he evaded them.
 
Since the war he has mostly worked as a freighter pilot, occasionally using his military training to get out of dodge. These days he - embittered that the war was every bit as terrible as he''d expected - just wants to retire, and forget space exists for a while. But, if he can, comfortably.
 
Phillip is a bit on the short side, and a little portly (having long since given up military level fitness). He is also relatively old - at least in his forties (assuming we''re 5-10 years after the war). He has lost quite a bit of the hair on top of his head.'
);

UPDATE player
SET stats_id = ${lastinsert}
WHERE name = 'Phillip Stuen';