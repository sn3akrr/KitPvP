<?php namespace kitpvp\duels;

class Structure{

	const ARENAS = [
		
	];

	const QUEUES = [
		"matched" => [
			"sameKit" => true,
			"arenas" => [
				"arena_1",
				"arena_2",
				"arena_3",
				"arena_4",
				"arena_5",
				"arena_6",
				"arena_7",
				"arena_8",
			],
		],
		"random" => [
			"sameKit" => false,
			"arenas" => [
				"arena_1",
				"arena_2",
				"arena_3",
				"arena_4",
				"arena_5",
				"arena_6",
				"arena_7",
				"arena_8",
			],
		],
	];

}