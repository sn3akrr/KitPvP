<?php namespace kitpvp\arena\predators;

class Structure{

	const LEVEL = "atm";

	const SPAWN_LIMITS = [
		"base" => 50,

		"knight" => 5,
		"pawn" => 5,
		"king" => 1,
		"robot" => 5,
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
		"western-1" => [
			"type" => "bandit",
			"ticks" => 5,
			"x" => 3053,
			"y" => 48,
			"z" => -49
		],
		"western-2" => [
			"type" => "cowboy",
			"ticks" => 13,
			"x" => 3088,
			"y" => 48,
			"z" => -61
		],
		"western-boss" => [
			"type" => "sheriff",
			"ticks" => 188,
			"distance" => -1,
			"x" => 3016,
			"y" => 50,
			"z" => -91
		],

		"castle-1" => [
			"type" => "pawn",
			"ticks" => 5,
			"x" => 3054,
			"y" => 70,
			"z" => 137
		],
		"castle-2" => [
			"type" => "knight",
			"ticks" => 10,
			"x" => 3062,
			"y" => 54,
			"z" => 135
		],
		"castle-boss" => [
			"type" => "king",
			"ticks" => 145,
			"distance" => -1,
			"x" => 3054,
			"y" => 48,
			"z" => 107
		],

		"mountains-1" => [
			"type" => "caveman",
			"ticks" => 5,
			"x" => 2992,
			"y" => 48,
			"z" => 19
		],
		"mountains-2" => [
			"type" => "jungleman",
			"ticks" => 12,
			"x" => 2969,
			"y" => 57,
			"z" => 6
		],
		"mountains-boss" => [
			"type" => "gorilla",
			"ticks" => 225,
			"distance" => -1,
			"x" => 2956,
			"y" => 62,
			"z" => 37
		],

		"city-1" => [
			"type" => "robot",
			"ticks" => 7,
			"distance" => 40,
			"x" => 3121,
			"y" => 48,
			"z" => 12
		],
		"city-2" => [
			"type" => "cyborg",
			"ticks" => 16,
			"distance" => 40,
			"x" => 3129,
			"y" => 48,
			"z" => 58
		],
		"city-boss" => [
			"type" => "powermech",
			"ticks" => 155,
			"distance" => -1,
			"x" => 3141,
			"y" => 48,
			"z" => 4
		],
	];

}