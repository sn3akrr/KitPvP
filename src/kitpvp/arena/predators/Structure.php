<?php namespace kitpvp\arena\predators;

class Structure{

	const LEVEL = "atm";

	const SPAWN_LIMITS = [
		"base" => 50,

		"knight" => 5,
		"bishop" => 5,
		"pawn" => 5,
		"king" => 1,
		"android" => 5,
		"cyborg" => 5,
		"powermech" => 1,
		"jungleman" => 5,
		"caveman" => 5,
		"gorilla" => 1,
		"bandit" => 5,
		"cowboy" => 5,
		"cowgirl" => 5,
		"sheriff" => 1
	];

	const LOCATIONS = [
		"id" => [
			"type" => "bandit",
			"ticks" => 5,
			"x" => 3054,
			"y" => 49,
			"z" => 21
		],

		"castle-1" => [
			"type" => "pawn",
			"ticks" => 5,
			"x" => 3054,
			"y" => 70,
			"z" => 137
		],
	];

}