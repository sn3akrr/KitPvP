<?php namespace kitpvp\duels\pieces;

use pocketmine\Player;

use kitpvp\KitPvP;

class MatchedQueue extends Queue{

	public function tick(){

	}

	public function addPlayer(Player $player, $pm = null){
		$this->players[$player->getName()] = [
			"map" => ($pm == null ? "none" : $pm),
			"kit" => KitPvP::getInstance()->getKits()->getPlayerKit($player)->getName()
		];
	}

}