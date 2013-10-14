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
  '',
  6,
  0,
  0,
  5,
  10,
  5,
  10,
  10,
  'k1 is a refugee from the dead planet Taiqwon. Taiqwon was the closest outer world to Alliance space before it was destroyed early in the war. Due to the planets unusual magnetic field electronics deteriorate at a much higher rate than they do on other worlds and after three years of difficult colonisation the planet was abandon save for a small town of a few thousand people that choose to stay. With no electronics life ' ${concat} 
  'on Taiqwon was very difficult which lead the planets few inhabitants to establish strange social structures and conventions. When children reach their fifth birthday they are sent to the Temple to learn the skills necessary to survive and earn their name. k1 was in his eleventh year at the Temple when the war started. The Alliance invaded Taiqwon as an example to the outer worlds killing all of its inhabitants except a single student at the Temple. k1 still does not know why he was spared although he imagines the Alliance soldier who killed the rest of his dorm mates simply expected him to' ${concat} 
  'die if left alone on a harsh planet. But he survived for several years until by chance he was found by a freighter that landed to make emergency repairs. When the ships crew asked him his name he told them his bunk number as he had never finished his training and earned a real name.',
  'To explore space and possibly find the Alliance solider who spared him.',
  'Because of his heritage k1 has no knowledge of machines or electronics, he drifts between ships as a laborer and occasionally as protection for valuable cargo. Some of skills learned in the Temple assist k1 in surviving in an unfamiliar environment, his heightened perception allows him to avoid danger and he is skilled in hand-to-hand combat and archery.'
);

UPDATE player
SET stats_id = ${lastinsert}
WHERE name = 'k1';