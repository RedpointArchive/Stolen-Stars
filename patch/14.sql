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
  6,
  0,
  0,
  4,
  4,
  12,
  8,
  12,
  'Civil war on his planet, he betrayed the king
Parents and father figure arrested/murdered
Was shot in the leg, permanatly slowed. Walks around with a cane
Slaughtered over 200 soldiers
Banished from home planet',
  'Primarily, Sergei is out for revenge on the people who drove him out of Kamchatka, and the inhabitants of the planet where his best friend was killed. He would like to rule over these two planets, and eventually expand into an empire.',
  'When Sergei was a child he lived on a small, icy planet called Kamchatka, ruled by King Romanov. King Romanov sent Sergei''s parents to prison when he was eight, claiming them to be spies. He was raised from there by another man from his town, who had extremist ideals. This man, Vladmir Smirnoff is the reason Sergei is as he is.
Two years later, after being taught of the evils of the king and his people from his new father figure, a massive galactic war broke out (as is common knowledge). The planet was largely involved in the war, and suffered massive losses. Due to this, the people grew irritated at the king of the planet and a resistance group appeared to attempt a revolution. Many small groups began to appear attempting to take control during the power struggle. Halfway through the war, Sergei''s planet pulled out of the conflict.
During the power struggle, those loyal to the king continued to rule the planet. Vladmir Smirnoff was killed by a man sent by King Romanov, which angered Sergei to no end. But he had been taught how to act in this situation. He linked up with a revolutionary group, and infiltrated the King''s Army with his new found friend Luka. He and Luka rose through the ranks rather quickly for being outstanding leaders, bringing themm closer and closer to the king. They would kill anyone who stood in his way. He had his first taste of power in this time.
The war still raged on outside of Kamchatka, but the power struggle within it seemed to have hit a stalemate. That was, of course, until Sergei and his men staged a Coup Detat. Swiftly, the king was overthrown and imprisoned. Sergei stayed in the military as the new regime took over. It was about this time, the war finally came to a sudden end.
Eventually, the new regime of Kamchatka launched an offensive against a neighbouring planet in order to secure more resources. Sergei and Luka led a team to attack a township, with the goal of both capturing the town and securing some POWs for interrogation. Casualties were high on both sides, with Sergei taking a shot to the leg, permanently slowing him. Luka was killed in action by a marksman. This angered Sergei.
When the enemy finally surrendered, with around 200 POWs, Sergei and his troops were still enraged. Sergei''s men beat and murdered many of the POWs, and any that tried to flee were also shot. A POW approached Sergei begging for mercy. After a couple of minutes, the POW got his response in the form of a bullet to the head. The entire enemy force were slaughtered.
As a result, Sergei was discharged and vilified on both planets. It was due to this that he left. He moved to another planet, where be was merely just another face in the crowd. He began to drink heavily. He got a job working for a politician, who he despises. He spends most of his time in a bar planning a triumphant return to his home planet, but this time, he would be the leader.'
);

UPDATE player
SET stats_id = ${lastinsert}
WHERE name = 'Sergei Abramovich';