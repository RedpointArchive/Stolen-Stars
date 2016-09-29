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
  'Engineer / Electrical Engineer',
  3,
  0,
  0,
  6,
  6,
  12,
  10,
  6,
  'Gaheris was contacted by aliens from another galaxy. This made him a laughing stock to the Alliance Miitary Research Facility where he worked.
 
He has an electronic-neural interface chip as well as minor genetic enhancements that prolong his life and reduce aging effects.',
  'Gaheris seeks to meet alien life again via the development of intergalactic space travel as well as cybernetic and genetic improvements to prolong his life.',
  'Gaheris grew up in the core worlds, he was genetically modified at birth. He was raised in a boarding school, quickly advanced in mechanical and electronic knowledge and was noticed by the Alliance military by his 15th birthday. 
 
The Alliance military used his talents to vastly improve their ships weaponry and combat capabilities as well as to restore ships that were damaged in the war against the outer worlds. They gave him an electronic-neural interface chip to enhance his work.
 
Two years ago he was contacted by an alien from another galaxy. He had spent less than a year at the Alliance Military Research Facility and had just turned 16. He contacted his superiors about the alien, requesting to start an intergalactic research division which was denied by his superiors.
 
After his coworkers found out about his "alien encounter" they laughed at him. He soon quit and began studies on High Speed Engines and Stasis Devices to try to find a means to transportation to other galaxies.
 
He''s bought a decomisioned ship from the Alliance with his work savings and is letting a motley crew use it for a small cut of whatever profits they make. He just wants to be left alone to his research but follow any leads he has on the aliens.'
);

UPDATE player
SET stats_id = ${lastinsert}
WHERE name = 'Gaheris Tesla';