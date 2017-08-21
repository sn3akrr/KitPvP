<?php namespace kitpvp\spectate;

use kitpvp\KitPvP;

class Spectate{

	public $plugin;

	public $spectating = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
	}

}