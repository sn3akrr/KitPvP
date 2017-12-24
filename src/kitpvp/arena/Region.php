<?php namespace kitpvp\arena;

class Region{

	public $name;
	public $positions;

	public function __construct($name, $positions = []){
		$this->name = $name;
		$this->positions = $positions;
	}

	public function getName(){
		return $this->name;
	}

	public function getPositions(){
		return $this->positions;
	}

	public function getRandomPosition(){
		return $this->positions[mt_rand(0,count($this->positions) - 1)];
	}

}