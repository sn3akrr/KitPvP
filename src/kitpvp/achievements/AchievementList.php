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
			"points" => self::POINTS_TIER3
		],

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
		"streak_of_life" => [
			"displayName" => "Streak of Life",
			"description" => "Die with a kill streak of exactly 42 in the arena",
			"points" => self::POINTS_TIER5
		],
		"streak_killer" => [
			"displayName" => "Streak Killer",
			"description" => "Kill a player with a streak of 5 or more",
			"points" => self::POINTS_TIER1
		],
	];
}