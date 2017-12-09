<?php namespace kitpvp\achievements;

class List{

	const POINTS_TIER1 = 100;
	const POINTS_TIER2 = 250;
	const POINTS_TIER3 = 500;
	const POINTS_TIER4 = 1000;
	const POINTS_TIER5 = 2500;

	const ACHIEVEMENTS = [
		//Duels
		"duel_1" => [
			"displayName" => "Duel 1",
			"description" => "Win 1 duel",
			"points" => self::POINTS_TIER1
		],
		"duel_2" => [
			"displayName" => "Duel 2",
			"description" => "Win 5 duels",
			"points" => self::POINTS_TIER2
		],
		"duel_3" => [
			"displayName" => "Duel 3",
			"description" => "Win 10 duels",
			"points" => self::POINTS_TIER3
		],
		"duel_4" => [
			"displayName" => "Duel 4",
			"description" => "Win 25 duels",
			"points" => self::POINTS_TIER4
		],
		"duel_5" => [
			"displayName" => "Duel 5",
			"description" => "Win 50 duels",
			"points" => self::POINTS_TIER4
		],
		"duel_6" => [
			"displayName" => "Duel 6",
			"description" => "Win 100 duels",
			"points" => self::POINTS_TIER5
		],


	];
}