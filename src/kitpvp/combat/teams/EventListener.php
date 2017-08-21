<?php namespace kitpvp\combat\teams;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent
};
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

class EventListener implements Listener{

	public $plugin;
	public $teams;

	public function __construct(KitPvP $plugin, Teams $teams){
		$this->plugin = $plugin;
		$this->teams = $teams;
	}

	public function onQuit(PlayerQuitEvent $e){
		$player = $e->getPlayer();
		if($this->teams->inTeam($player)){
			$this->teams->disbandTeam($this->teams->getPlayerTeamUid($player));
		}
		$this->teams->closeTeamRequestsTo($player);
		$this->teams->closeTeamRequestsFrom($player);
	}

	/*public function onDmg(EntityDamageEvent $e){
		$teams = $this->teams;
		$victim = $e->getEntity();
		if($victim instanceof Player){
			if($e instanceof EntityDamageByEntityEvent){
				$killer = $e->getDamager();
				if($killer instanceof Player){
					if($teams->inTeam($victim) && $teams->inTeam($killer)){
						if($teams->getPlayerTeamUid($victim) == $teams->getPlayerTeamUid($killer)){
							$e->setCancelled(true);
							$killer->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED.$victim->getName()." is on your team!");
						}
					}
				}
			}
		}
	}*/

}