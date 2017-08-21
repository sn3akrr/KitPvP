<?php namespace KitPvP;

use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerPreLoginEvent,
	PlayerLoginEvent,
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

	public function onPreLogin(PlayerPreLoginEvent $e){
		$id = Core::getInstance()->getNetwork()->getIdentifier();
		if(count($this->plugin->getServer()->getOnlinePlayers()) >= 70){
			switch($id){
				case "kitpvp-1":
					$player->transferTo("kitpvp-2");
				break;
				case "kitpvp-2":
					$player->transferTo("kitpvp-3");
				break;
				case "kitpvp-3":
					$player->transferTo("lobby-1");
				break;
			}
		}
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
			if(!$this->plugin->getArena()->inArena($player)) $player->teleport(new Position(129.5,22,135.5, $this->plugin->getServer()->getLevelByName("KitSpawn")), 180);
		}
	}

	public function onDropItem(PlayerDropItemEvent $e){
		$e->setCancelled();
	}

	public function onQuit(PlayerQuitEvent $e){
		$this->plugin->getKits()->setEquipped($e->getPlayer(), false);
	}

	public function onInteract(PlayerInteractEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		if($block->getX() == 96 && $block->getY() == 21 && $block->getZ() == 110){
			$lb = $this->plugin->getLeaderboard();
			if($lb->getType($player) == 3){
				$lb->setType($player, 0);
			}else{
				$lb->setType($player, $lb->getType($player) + 1);
			}
			Core::getInstance()->getEntities()->getFloatingText()->forceUpdate($player);
		}
	}

	public function onPlace(BlockPlaceEvent $e){
		$e->setCancelled();
	}

	public function onBreak(BlockBreakEvent $e){
		$e->setCancelled();
	}

}