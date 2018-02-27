<?php namespace kitpvp;

use kitpvp\KitPvP;

use core\AtPlayer;

class KitPvPPlayer extends AtPlayer{

	public function getTechits(){
		return KitPvP::getInstance()->getTechits()->getSession($this)->getTechits();
	}

	public function takeTechits($value){
		KitPvP::getInstance()->getTechits()->getSession($this)->takeTechits($value);
	}

	public function addTechits($value){
		KitPvP::getInstance()->getTechits()->getSession($this)->addTechits($value);
	}

}