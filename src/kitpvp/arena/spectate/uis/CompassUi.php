<?php namespace kitpvp\arena\spectate\uis;

use pocketmine\{
	Player,
	Server
};
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

class CompassUi extends SimpleForm{

	public $players = [];

	public function __construct(Player $player){
		parent::__construct("Compass", "Select a player to teleport!");
		$level = Server::getInstance()->getLevelByName("atm");
		foreach($level->getPlayers() as $p){
			if($player != $p){
				$this->players[] = $p->getName();
				$this->addButton(new Button($p->getName() . PHP_EOL . round($p->distance($player), 2) . " blocks away"));
			}
		}
		$this->addButton(new Button("Exit compass"));
	}

	public function handle($response, Player $player){
		foreach($this->players as $key => $p){
			if($key == $response){
				$pl = Server::getInstance()->getPlayerExact($p);
				if(!$pl instanceof Player || !KitPvP::getInstance()->getArena()->inArena($pl)){
					$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "This player is no longer in the arena.");
					return;
				}
				if(!KitPvP::getInstance()->getArena()->getSpectate()->isSpectating($player)){
					$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You cannot use this menu because you are no longer in spectator mode!");
					return;
				}
				$player->teleport($pl);
				$player->sendMessae(TextFormat::GREEN . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Teleported to " . TextFormat::YELLOW . $p);
				return;
			}
		}
	}

}