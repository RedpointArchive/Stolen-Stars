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
  'Frontline combatant, Motivator, Preacher',
  3,
  0,
  0,
  10,
  10,
  4,
  12,
  4,
  'She was taken in by a group of organised criminals after she awoke and killed several lawmen upon being found naked in an alley in an outer planet. Called The Watchers, this organisation put her skills to good use as a heavy enforcer, a role she didn''t mind because with no Imperial citizens to protect, any act that brings her closer to her goal is justified. However, in a dinner in which she was invited to meet the high ranking members of the group in celebration of her success at defending a critical shipment of goods from the law, she was persuaded to share her story. A good deal of laughter ensued, and when one spoke that there was obviously no such thing as a true emperor all present agreed with him heartily, another going on to say it was good that she was no longer serving him.
 
She has since left the planet, and has two enemies and an ally there - the local law enforcement and The Watchers would very much like her dead, and the Riversnakes (a rival criminal organisation) are very amenable to her in gratitude for slaughtering the entirety of The Watchers'' high command for heresy.',
  'Preach the word of the Emperor wherever possible, find ways to amass greater power so that she can change the foolishly open minded ways of this system''s humans, and destroy any artificial intelligence, alien technology or presence and any followers of chaos that she comes across.
 
Optimally, she wishes to find a way to contact the Imperium direct an influx of missionaries, tech priests and guardsmen in order to convert, appropriate or destroy the technology of and pacify this wayward galaxy.',
  'A tall, stern featured woman in her late thirties with a stern face criss crossed by faded scars, red hair turning prematurely grey and an incredibly nice hat, Sabbatine is a warrior dedicated with every fibre of her being to an empire that does not exist.
 
From her perspective, she is a Commissar - a hardened political officer, one trained from birth in the Schola Progenium to lead from the front and execute any guardsman who dishonours the emperor by showing cowardice. Twenty years into a successful career and attached to a squad in the Vostroyan 209th as they defended a planet from orkish infestation, she had a brief second to pray to the emperor for forgiveness before was enveloped whole by a blast from a shokk attack gun, a weapon known to teleport its victims straight into the warp.
 
The next thing she knew, she was opening her eyes in an alley on a strange world, naked and missing a kidney. Questioning the inhabitants of the odd city regarding the location closest imperial guard outpost revealed that she was outside the emperor''s light, on a world that had never even heard of the blessed Imperium. Making her way using her hard earned battlefield prowess in an environment that seemed to find no end of need for warriors, she has desperately searched for some sign of the Imperium, some shred of hope that she might find her way home. Those she shares her story with come to a very different conclusion than she has, that her undeniable skills in combat and entirely incorrect memory mean she was a soldier in the war who has fallen victim to some strange experiment or mental trauma - but she knows the truth.
 
Somehow, she has been transported to a system that does not know the Emperor''s beneficence - a system strife with heresy, with rumours of aliens and artificial intelligence corrupting mankind - and she needs to find a way to put things right. And no amount of logic, of evidence that her memories are of a place that does not and could never have existed, will shake her faith in the Emperor.'
);

UPDATE player
SET stats_id = last_insert_rowid()
WHERE name = 'Sabbatine Holtz';