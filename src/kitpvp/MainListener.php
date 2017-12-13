<?php namespace KitPvP;

use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerJoinEvent,
	PlayerMoveEvent,
	PlayerDropItemEvent,
	PlayerQuitEvent,
	PlayerInteractEvent
};
use pocketmine\event\block\{
	BlockPlaceEvent,
	BlockBreakEvent
};

use pocketmine\network\protocol\mcpe\InteractPacket;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;

use core\Core;

class MainListener implements Listener{

	public $plugin;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $e){
		$player = $e->getPlayer();
		$player->teleport(new Position(129.5,22,135.5, $this->plugin->getServer()->getLevelByName("KitSpawn")), 180);

		if(!$this->plugin->getLeaderboard()->hasStats($player)) $this->plugin->getLeaderboard()->newStats($player);
	}

	public function onMove(PlayerMoveEvent $e){
		$player = $e->getPlayer();
		if($player->getLevel()->getBlockIdAt($player->getX(),$player->getY() + 1,$player->getZ()) == 90) $this->plugin->getArena()->tpToArena($player);
		if($player->y <= 17){
			if(!$this->plugin->getArena()->inArena($player)) if(!$this->plugin->getDuels()->inDuel($player)) $player->teleport(new Position(129.5,22,135.5, $this->plugin->getServer()->getLevelByName("KitSpawn")), 180);
		}
		$duels = $this->plugin->getDuels();
		if($duels->inDuel($player)){
			$duel = $duels->getPlayerDuel($player);
			if($duel->getGameStatus() == 0){
				$to = $e->getTo();
				$from = $e->getFrom();

				if($to->getX() != $from->getX() || $to->getZ() != $from->getZ()){
					$e->setCancelled(true);
				}
			}
		}
	}

	public function onDropItem(PlayerDropItemEvent $e){
		$e->setCancelled();
	}

	public function onQuit(PlayerQuitEvent $e){
		$player = $e->getPlayer();
		$this->plugin->getKits()->setEquipped($player, false);

		$duels = $this->plugin->getDuels();
		$duels->onQuit($player);
	}

	public function onInteract(PlayerInteractEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		if($block->getX() == 120 && $block->getY() == 21 && $block->getZ() == 83){
			$lb = $this->plugin->getLeaderboard();
			if($lb->getType($player) == 2){
				$lb->setType($player, 0);
			}else{
				$lb->setType($player, $lb->getType($player) + 1);
			}

			foreach(["weekly","alltime","monthly"] as $date){
				for($i = 1; $i <= 5; $i++){
					Core::getInstance()->getEntities()->getFloatingText()->getText($date . "-" . $i)->update($player, true);
				}
				Core::getInstance()->getEntities()->getFloatingText()->getText($date . "-you")->update($player, true);
				Core::getInstance()->getEntities()->getFloatingText()->getText("leaderboard-type")->update($player, true);
			}
		}
	}

	public function onPlace(BlockPlaceEvent $e){
		$e->setCancelled();
	}

	public function onBreak(BlockBreakEvent $e){
		$e->setCancelled();
	}

}