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
			"points" => self::POINTS_TIER0
		],
		"team_3" => [
			"displayName" => "Avenger",
			"description" => "Kill the player that killed your teammate",
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
			"description" => "Get 1 kill in the arena",
			"points" => self::POINTS_TIER1
		],
		"kills_2" => [
			"displayName" => "Having fun?",
			"description" => "Get 5 kills in the arena",
			"points" => self::POINTS_TIER1
		],
		"kills_3" => [
			"displayName" => "Kills 3",
			"description" => "Get 10 kills in the arena",
			"points" => self::POINTS_TIER1
		],
		"kills_4" => [
			"displayName" => "Kills 4",
			"description" => "Get 25 kills in the arena",
			"points" => self::POINTS_TIER2
		],
		"kills_5" => [
			"displayName" => "Kills 5",
			"description" => "Get 50 kills in the arena",
			"points" => self::POINTS_TIER2
		],
		"kills_6" => [
			"displayName" => "Kills 6",
			"description" => "Get 100 kills in the arena",
			"points" => self::POINTS_TIER3
		],
		"kills_7" => [
			"displayName" => "Kills 7",
			"description" => "Get 250 kills in the arena",
			"points" => self::POINTS_TIER4
		],
		"kills_8" => [
			"displayName" => "Rampaged Savage",
			"description" => "Get 500 kills in the arena",
			"points" => self::POINTS_TIER4
		],
		"kills_9" => [
			"displayName" => "Block Game Butcher",
			"description" => "Get 750 kills in the arena",
			"points" => self::POINTS_TIER5
		],
		"kills_10" => [
			"displayName" => "Serial Killer",
			"description" => "Get 1000 kills in the arena",
			"points" => self::POINTS_TIER5
		],

		//Streak stuff
		"streak_1" => [
			"displayName" => "Streak 1",
			"description" => "Get a 5 kill streak in the arena",
			"points" => self::POINTS_TIER1
		],
		"streak_2" => [
			"displayName" => "Streak 2",
			"description" => "Get a 10 kill streak in the arena",
			"points" => self::POINTS_TIER2
		],
		"streak_3" => [
			"displayName" => "Streak 3",
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
			"points" => self::POINTS_TIER0,
		],
		"wasted" => [
			"displayName" => "Waste of techits",
			"description" => "Die with 0 kills with any kit other than Noob in the arena",
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
			"points" => self::POINTS_TIER2,
		],
		"archer_gun" => [
			"displayName" => "2 can play at that game",
			"description" => "Shoot an archer with a gun",
			"points" => self::POINTS_TIER1,
		],
		"enchanter_kill" => [
			"displayName" => "What doesn't kill me makes me stronger",
			"description" => "Kill someone with the Enchanter kit using the Book of Spells",
			"points" => self::POINTS_TIER1,
		],
		"3_on_killer" => [
			"displayName" => "You don't know who you're messing with",
			"description" => "Kill someone 3 times that killed you once",
			"points" => self::POINTS_TIER2,
		],
		"pyromancer_drown" => [
			"displayName" => "My one weakness",
			"description" => "Drown as Pyromancer",
			"points" => self::POINTS_TIER1,
		],
		"countered" => [
			"displayName" => "Get countered!",
			"description" => "Land a Concussion Grenade on someone with the Scout kit",
			"points" => self::POINTS_TIER2,
		],
		"faker" => [
			"displayName" => "Faker",
			"description" => "Shoot 10 arrows and miss all of them",
			"points" => self::POINTS_TIER1
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
	];
}