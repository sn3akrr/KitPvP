<?php namespace kitpvp\achievements;

class AchievementList{

	const POINTS_TIER0 = 50;
	const POINTS_TIER1 = 100;
	const POINTS_TIER2 = 250;
	const POINTS_TIER3 = 500;
	const POINTS_TIER4 = 1000;
	const POINTS_TIER5 = 2500;

	const ACHIEVEMENTS = [
		"team_1" => [
			"displayName" => "Team pls",
			"description" => "Team with another player",
			"points" => self::POINTS_TIER0
		],
		"team_2" => [
			"displayName" => "i hate u",
			"description" => "Manually disband your team",
			"hidden" => true,
			"points" => self::POINTS_TIER0
		],
		"team_3" => [
			"displayName" => "Avenger",
			"description" => "Kill the player that killed your teammate in the arena",
			"hidden" => true,
			"points" => self::POINTS_TIER0
		],
		"team_4" => [
			"displayName" => "Best Frenemies",
			"description" => "Team up with the person who killed you last",
			"hidden" => true,
			"points" => self::POINTS_TIER2
		],

		//Duel stuff
		"duel_1" => [
			"displayName" => "First Victory!",
			"description" => "Win 1 duel",
			"points" => self::POINTS_TIER0
		],
		"duel_2" => [
			"displayName" => "Average Dueler",
			"description" => "Win 5 duels",
			"points" => self::POINTS_TIER1
		],
		"duel_3" => [
			"displayName" => "Git gg10'd",
			"description" => "Win 10 duels",
			"points" => self::POINTS_TIER2
		],
		"duel_4" => [
			"displayName" => "Do you even duel?",
			"description" => "Win 25 duels",
			"points" => self::POINTS_TIER3
		],
		"duel_5" => [
			"displayName" => "Let's settle it.. 1v1 style!",
			"description" => "Win 50 duels",
			"points" => self::POINTS_TIER4
		],
		"duel_6" => [
			"displayName" => "FITE ME BRO!",
			"description" => "Win 100 duels",
			"points" => self::POINTS_TIER5
		],

		//Kills stuff
		"kills_1" => [
			"displayName" => "First Murder!",
			"description" => "Kill 1 player in the arena",
			"points" => self::POINTS_TIER1
		],
		"kills_2" => [
			"displayName" => "Having fun?",
			"description" => "Kill 5 players in the arena",
			"points" => self::POINTS_TIER1
		],
		"kills_3" => [
			"displayName" => "Killer!",
			"description" => "Kill 10 players in the arena",
			"points" => self::POINTS_TIER1
		],
		"kills_4" => [
			"displayName" => "#NoEmpathy",
			"description" => "Kill 25 players in the arena",
			"points" => self::POINTS_TIER2
		],
		"kills_5" => [
			"displayName" => "Mansaughter",
			"description" => "Kill 50 players in the arena",
			"points" => self::POINTS_TIER2
		],
		"kills_6" => [
			"displayName" => "What's that smell?",
			"description" => "Kill 100 players in the arena",
			"points" => self::POINTS_TIER3
		],
		"kills_7" => [
			"displayName" => "Bloodbath Time!",
			"description" => "Kill 250 players in the arena",
			"points" => self::POINTS_TIER4
		],
		"kills_8" => [
			"displayName" => "Rampaged Savage",
			"description" => "Kill 500 players in the arena",
			"points" => self::POINTS_TIER4
		],
		"kills_9" => [
			"displayName" => "Block Game Butcher",
			"description" => "Kill 750 players in the arena",
			"points" => self::POINTS_TIER5
		],
		"kills_10" => [
			"displayName" => "Serial Killer",
			"description" => "Kill 1000 players in the arena",
			"points" => self::POINTS_TIER5
		],

		//Streak stuff
		"streak_1" => [
			"displayName" => "On a roll!",
			"description" => "Get a 5 kill streak in the arena",
			"points" => self::POINTS_TIER1
		],
		"streak_2" => [
			"displayName" => "Monster!",
			"description" => "Get a 10 kill streak in the arena",
			"points" => self::POINTS_TIER2
		],
		"streak_3" => [
			"displayName" => "Rampage!",
			"description" => "Get a 20 kill streak in the arena",
			"points" => self::POINTS_TIER3
		],
		"streak_4" => [
			"displayName" => "Unstoppable!",
			"description" => "Get a 25 kill streak in the arena",
			"points" => self::POINTS_TIER4
		],
		"malone_streak" => [
			"displayName" => "Malone's Number",
			"description" => "Die with a kill streak of exactly 23 in the arena",
			"hidden" => true,
			"points" => self::POINTS_TIER4
		],
		"streak_killer" => [
			"displayName" => "Streak Killer",
			"description" => "Kill a player with a streak of 5 or more",
			"points" => self::POINTS_TIER1
		],

		//Kit+Arena stuff
		"lol_noob" => [
			"displayName" => "Lol, Noob!",
			"description" => "Die with 0 kills as the Noob kit in the arena",
			"hidden" => true,
			"points" => self::POINTS_TIER0,
		],
		"wasted" => [
			"displayName" => "Waste of techits",
			"description" => "Die with 0 kills with any kit other than Noob in the arena",
			"hidden" => true,
			"points" => self::POINTS_TIER1,
		],
		"close_call" => [
			"displayName" => "Close call!",
			"description" => "Kill a player with under 2 hearts left",
			"points" => self::POINTS_TIER2,
		],
		"multiple_flamethrower" => [
			"displayName" => "Spreading flames",
			"description" => "Ignite 2 or more players with one Flame ball (Flamethrower)",
			"hidden" => true,
			"points" => self::POINTS_TIER2,
		],
		"archer_gun" => [
			"displayName" => "2 can play at that game",
			"description" => "Shoot an archer with a gun",
			"hidden" => true,
			"points" => self::POINTS_TIER1,
		],
		"noob_malone" => [
			"displayName" => "Wh... Wha... HOW?",
			"description" => "Get killed by a noob as m4l0ne23",
			"points" => self::POINTS_TIER0
		],
		"countered" => [
			"displayName" => "Get countered!",
			"description" => "Land a Concussion Grenade on someone with the Scout kit",
			"hidden" => true,
			"points" => self::POINTS_TIER2,
		],
		"faker" => [
			"displayName" => "Faker",
			"description" => "Shoot 10 arrows and miss all of them",
			"hidden" => true,
			"points" => self::POINTS_TIER1
		],
		"perfect" => [
			"displayName" => "Perfect Kill!",
			"description" => "Kill someone without losing any health",
			"points" => self::POINTS_TIER3
		],

		//First Equips
		"noob_first" => [
			"displayName" => "Beginner",
			"description" => "Equip the Noob kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"witch_first" => [
			"displayName" => "It's Magical",
			"description" => "Equip the Witch kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"spy_first" => [
			"displayName" => "Master of Stealth",
			"description" => "Equip the Spy kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"scout_first" => [
			"displayName" => "Hit and Run",
			"description" => "Equip the Scout kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"assault_first" => [
			"displayName" => "Run 'em and Gun 'em",
			"description" => "Equip the Assault kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"medic_first" => [
			"displayName" => "Medical Madness",
			"description" => "Equip the Medic kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"archer_first" => [
			"displayName" => "Ready, Aim, Fire!",
			"description" => "Equip the Archer kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"enderman_first" => [
			"displayName" => "I'm here, now I'm there",
			"description" => "Equip the Enderman kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"pyromancer_first" => [
			"displayName" => "All shall burn",
			"description" => "Equip the Pyromancer kit for the first time",
			"points" => self::POINTS_TIER0
		],
		"malone_first" => [
			"displayName" => "I'm the owner!",
			"description" => "Equip the m4l0ne23 kit for the first time",
			"points" => self::POINTS_TIER0
		],

		//Envoys
		"envoy_1" => [
			"displayName" => "Free stuff!",
			"description" => "Collect 1 envoy in the arena",
			"points" => self::POINTS_TIER0
		],
		"envoy_2" => [
			"displayName" => "On a mission",
			"description" => "Collect 5 envoys in the arena",
			"points" => self::POINTS_TIER1
		],
		"envoy_3" => [
			"displayName" => "Treasure Hunter",
			"description" => "Collect 25 envoys in the arena",
			"points" => self::POINTS_TIER2
		],
		"envoy_4" => [
			"displayName" => "All ur loot belong to me",
			"description" => "Collect 100 envoys in the arena",
			"points" => self::POINTS_TIER3
		],
		"envoy_5" => [
			"displayName" => "Master of Greed",
			"description" => "Collect 1000 envoys in the arena",
			"points" => self::POINTS_TIER5
		],

		//Predators
		/*"knight_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Knight in the arena",
			"points" => self::POINTS_TIER0
		],
		"knight_2" => [
			"displayName" => " ",
			"description" => "Kill 25 Knights in the arena",
			"points" => self::POINTS_TIER1
		],
		"knight_3" => [
			"displayName" => " ",
			"description" => "Kill 100 Knights in the arena",
			"points" => self::POINTS_TIER3
		],
		"knight_4" => [
			"displayName" => " ",
			"description" => "Kill 1000 Knights in the arena",
			"points" => self::POINTS_TIER4
		],
		"knight_5" => [
			"displayName" => " ",
			"description" => "Kill 10000 Knights in the arena",
			"points" => self::POINTS_TIER5
		],
		"knight_die" => [
			"displayName" => " ",
			"description" => "Get killed by a Knight",
			"points" => self::POINTS_TIER0
		],

		"pawn_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Pawn in the arena",
			"points" => self::POINTS_TIER0
		],
		"pawn_2" => [
			"displayName" => " ",
			"description" => "Kill 25 Pawns in the arena",
			"points" => self::POINTS_TIER1
		],
		"pawn_3" => [
			"displayName" => " ",
			"description" => "Kill 100 Pawns in the arena",
			"points" => self::POINTS_TIER3
		],
		"pawn_4" => [
			"displayName" => "Pure evil!",
			"description" => "Kill 1000 Pawns in the arena",
			"points" => self::POINTS_TIER4
		],
		"pawn_5" => [
			"displayName" => "Checkmate.",
			"description" => "Kill 10000 Pawns in the arena",
			"points" => self::POINTS_TIER5
		],
		"pawn_die" => [
			"displayName" => " ",
			"description" => "Get killed by a Pawn",
			"points" => self::POINTS_TIER0
		],

		"robot_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Robot in the arena",
			"points" => self::POINTS_TIER0
		],
		"robot_2" => [
			"displayName" => " ",
			"description" => "Kill 25 Robots in the arena",
			"points" => self::POINTS_TIER1
		],
		"robot_3" => [
			"displayName" => " ",
			"description" => "Kill 100 Robots in the arena",
			"points" => self::POINTS_TIER3
		],
		"robot_4" => [
			"displayName" => " ",
			"description" => "Kill 1000 Robots in the arena",
			"points" => self::POINTS_TIER4
		],
		"robot_5" => [
			"displayName" => "Short Circuited",
			"description" => "Kill 10000 Robots in the arena",
			"points" => self::POINTS_TIER5
		],
		"robot_die" => [
			"displayName" => "CODE RED",
			"description" => "Get killed by a Robot",
			"points" => self::POINTS_TIER0
		],

		"cyborg_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Cyborg in the arena",
			"points" => self::POINTS_TIER0
		],
		"cyborg_2" => [
			"displayName" => " ",
			"description" => "Kill 25 Cyborgs in the arena",
			"points" => self::POINTS_TIER1
		],
		"cyborg_3" => [
			"displayName" => " ",
			"description" => "Kill 100 Cyborgs in the arena",
			"points" => self::POINTS_TIER3
		],
		"cyborg_4" => [
			"displayName" => " ",
			"description" => "Kill 1000 Cyborgs in the arena",
			"points" => self::POINTS_TIER4
		],
		"cyborg_5" => [
			"displayName" => " ",
			"description" => "Kill 10000 Cyborgs in the arena",
			"points" => self::POINTS_TIER5
		],
		"cyborg_die" => [
			"displayName" => "",
			"description" => "Get killed by a Cyborg",
			"points" => self::POINTS_TIER0
		],

		"jungleman_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Jungleman in the arena",
			"points" => self::POINTS_TIER0
		],
		"jungleman_2" => [
			"displayName" => " ",
			"description" => "Kill 25 Junglemens in the arena",
			"points" => self::POINTS_TIER1
		],
		"jungleman_3" => [
			"displayName" => " ",
			"description" => "Kill 100 Junglemens in the arena",
			"points" => self::POINTS_TIER3
		],
		"jungleman_4" => [
			"displayName" => " ",
			"description" => "Kill 1000 Junglemens in the arena",
			"points" => self::POINTS_TIER4
		],
		"jungleman_5" => [
			"displayName" => " ",
			"description" => "Kill 10000 Junglemens in the arena",
			"points" => self::POINTS_TIER5
		],
		"jungleman_die" => [
			"displayName" => " ",
			"description" => "Get killed by a Jungleman",
			"points" => self::POINTS_TIER0
		],

		"caveman_1" => [
			"displayName" => "Evolution",
			"description" => "Kill 1 Caveman in the arena",
			"points" => self::POINTS_TIER0
		],
		"caveman_2" => [
			"displayName" => " ",
			"description" => "Kill 25 Cavemen in the arena",
			"points" => self::POINTS_TIER1
		],
		"caveman_3" => [
			"displayName" => " ",
			"description" => "Kill 100 Cavemen in the arena",
			"points" => self::POINTS_TIER3
		],
		"caveman_4" => [
			"displayName" => " ",
			"description" => "Kill 1000 Cavemen in the arena",
			"points" => self::POINTS_TIER4
		],
		"caveman_5" => [
			"displayName" => " ",
			"description" => "Kill 10000 Cavemen in the arena",
			"points" => self::POINTS_TIER5
		],
		"caveman_die" => [
			"displayName" => " ",
			"description" => "Get killed by a Caveman",
			"points" => self::POINTS_TIER0
		],

		"bandit_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Bandit in the arena",
			"points" => self::POINTS_TIER0
		],
		"bandit_2" => [
			"displayName" => " ",
			"description" => "Kill 25 Bandits in the arena",
			"points" => self::POINTS_TIER1
		],
		"bandit_3" => [
			"displayName" => "",
			"description" => "Kill 100 Bandits in the arena",
			"points" => self::POINTS_TIER3
		],
		"bandit_4" => [
			"displayName" => " ",
			"description" => "Kill 1000 Bandits in the arena",
			"points" => self::POINTS_TIER4
		],
		"bandit_5" => [
			"displayName" => "The New Sheriff",
			"description" => "Kill 10000 Bandits in the arena",
			"points" => self::POINTS_TIER5
		],
		"bandit_die" => [
			"displayName" => " ",
			"description" => "Get killed by a Bandit",
			"points" => self::POINTS_TIER0
		],

		"cowboy_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Cowboy in the arena",
			"points" => self::POINTS_TIER0
		],
		"cowboy_2" => [
			"displayName" => " ",
			"description" => "Kill 25 Cowboys in the arena",
			"points" => self::POINTS_TIER1
		],
		"cowboy_3" => [
			"displayName" => " ",
			"description" => "Kill 100 Cowboys in the arena",
			"points" => self::POINTS_TIER3
		],
		"cowboy_4" => [
			"displayName" => " ",
			"description" => "Kill 1000 Cowboys in the arena",
			"points" => self::POINTS_TIER4
		],
		"cowboy_5" => [
			"displayName" => "The Night Rider",
			"description" => "Kill 10000 Cowboys in the arena",
			"points" => self::POINTS_TIER5
		],
		"cowboy_die" => [
			"displayName" => " ",
			"description" => "Get killed by a Cowboy",
			"points" => self::POINTS_TIER0
		],

		"king_1" => [
			"displayName" => "Overthrown",
			"description" => "Kill 1 King Boss in the arena",
			"points" => self::POINTS_TIER2
		],
		"king_2" => [
			"displayName" => "Fallen Kingdom",
			"description" => "Kill 50 King Bosses in the arena",
			"points" => self::POINTS_TIER3
		],
		"king_3" => [
			"displayName" => "King Slayer",
			"description" => "Kill 250 King Bosses in the arena",
			"points" => self::POINTS_TIER4
		],
		"king_death" => [
			"displayName" => "Off with your head!",
			"description" => "Get killed by the King Boss",
			"points" => self::POINTS_TIER0
		],

		"powermech_1" => [
			"displayName" => "Does not compute",
			"description" => "Kill 1 PowerMech Boss in the arena",
			"points" => self::POINTS_TIER2
		],
		"powermech_2" => [
			"displayName" => " ",
			"description" => "Kill 50 PowerMech Bosses in the arena",
			"points" => self::POINTS_TIER3
		],
		"powermech_3" => [
			"displayName" => " ",
			"description" => "Kill 250 PowerMech Bosses in the arena",
			"points" => self::POINTS_TIER4
		],
		"powermech_death" => [
			"displayName" => "Overpowered",
			"description" => "Get killed by the PowerMech Boss",
			"points" => self::POINTS_TIER0
		],

		"gorilla_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Gorilla Boss in the arena",
			"points" => self::POINTS_TIER2
		],
		"gorilla_2" => [
			"displayName" => " ",
			"description" => "Kill 50 Gorilla Bosses in the arena",
			"points" => self::POINTS_TIER3
		],
		"gorilla_3" => [
			"displayName" => " ",
			"description" => "Kill 250 Gorilla Bosses in the arena",
			"points" => self::POINTS_TIER4
		],
		"gorilla_death" => [
			"displayName" => "Harambe's Revenge",
			"description" => "Get killed by the Gorilla Boss",
			"points" => self::POINTS_TIER0
		],

		"sheriff_1" => [
			"displayName" => " ",
			"description" => "Kill 1 Sheriff Boss in the arena",
			"points" => self::POINTS_TIER2
		],
		"sheriff_2" => [
			"displayName" => " ",
			"description" => "Kill 50 Sheriff Bosses in the arena",
			"points" => self::POINTS_TIER3
		],
		"sheriff_3" => [
			"displayName" => " ",
			"description" => "Kill 250 Sheriff Bosses in the arena",
			"points" => self::POINTS_TIER4
		],
		"sheriff_death" => [
			"displayName" => "Under Arrest",
			"description" => "Get killed by the Sheriff Boss",
			"points" => self::POINTS_TIER0
		],


		*/

		"this_town" => [
			"displayName" => "This town ain't big enough",
			"description" => "Shoot a cowboy with a gun",
			"points" => self::POINTS_TIER2
		],

		//Bosses
	];
}