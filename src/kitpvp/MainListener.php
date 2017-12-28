<?php namespace KitPvP;

use pocketmine\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerJoinEvent,
	PlayerMoveEvent,
	PlayerDropItemEvent,
	PlayerQuitEvent,
	PlayerInteractEvent,
	PlayerJumpEvent
};
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent
};
use pocketmine\event\block\{
	BlockPlaceEvent,
	BlockBreakEvent
};
use pocketmine\level\sound\GhastShootSound;

use pocketmine\network\protocol\mcpe\InteractPacket;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;

use kitpvp\uis\MainUi;

use core\Core;

class MainListener implements Listener{

	public $plugin;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $e){
		$player = $e->getPlayer();
		$player->teleport(...$this->plugin->getArena()->getSpawnPosition());

		$this->plugin->getDuels()->createSession($player);
		$this->plugin->getKits()->createSession($player);
		$this->plugin->getAchievements()->createSession($player);
		if(!$this->plugin->getLeaderboard()->hasStats($player)) $this->plugin->getLeaderboard()->newStats($player);
	}

	public function onMove(PlayerMoveEvent $e){
		$player = $e->getPlayer();
		if($player->getLevel()->getBlockIdAt($player->getX(),$player->getY() + 1,$player->getZ()) == 90) $this->plugin->getArena()->tpToArena($player);
		if($player->y <= 49){
			if($this->plugin->getArena()->inSpawn($player)) $player->teleport(...$this->plugin->getArena()->getSpawnPosition());
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

		$this->plugin->getKits()->deleteSession($player);
		$this->plugin->getAchievements()->deleteSession($player);
		$duels = $this->plugin->getDuels();
		$duels->onQuit($player);

		unset($this->plugin->jump[$player->getName()]);
	}

	public function onInteract(PlayerInteractEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		if($this->plugin->getArena()->inSpawn($player) && $block->getX() == 62 && $block->getY() == 59 && $block->getZ() == 143){
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
			}
			Core::getInstance()->getEntities()->getFloatingText()->getText("leaderboard-type")->update($player, true);
		}
	}

	public function onJump(PlayerJumpEvent $e){
		$player = $e->getPlayer();
		if(!isset($this->plugin->jump[$player->getName()]) && $player->getY() >= 64 && $this->plugin->getArena()->inSpawn($player)){
			$dv = $player->getDirectionVector();
			$player->knockback($player, 0, $dv->x, $dv->z, 0.9);
			$player->getLevel()->addSound(new GhastShootSound($player));
			$this->plugin->jump[$player->getName()] = true;

			$attribute = $player->getAttributeMap()->getAttribute(5);
			$attribute->setValue($attribute->getValue() * (1 + 0.2 * 5), true);
		}
	}

	public function onDmg(EntityDamageEvent $e){
		$player = $e->getEntity();
		if($player instanceof Player){
			if($e instanceof EntityDamageByEntityEvent){
				$target = $e->getDamager();
				if($target instanceof Player){
					if($this->plugin->getArena()->inSpawn($target)){
						if($target->getName() == "m4l0ne23") $target->showModal(new MainUi($player));
					}
				}
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