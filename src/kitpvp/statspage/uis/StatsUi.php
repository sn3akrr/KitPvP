<?php namespace kitpvp\statspage\uis;

use kitpvp\KitPvP;

use core\ui\windows\CustomForm;
use core\ui\elements\customForm\Label;
use core\stats\User;

class StatsUi extends CustomForm{

	public $user;

	public function __construct($user){
		$this->user = new User($user);

		parent::__construct($this->user->getGamertag() . "'s stats");

		$user = $this->user;
		if(!$user->validPlayer()){

		}else{

		}
	}

	public function handle($response, Player $player){

	}

}