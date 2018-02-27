<?php namespace kitpvp;

use pocketmine\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerCreationEvent,

	PlayerJoinEvent,
	PlayerMoveEvent,
	PlayerDropItemEvent,
	PlayerQuitEvent,
	PlayerInteractEvent,
	PlayerJumpEvent
};
use pocketmine\event\inventory\{
	InventoryPickupItemEvent,
	InventoryTransactionEvent
};
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent
};
use pocketmine\event\block\{
	BlockPlaceEvent,
	BlockBreakEvent
};
use pocketmine\item\Item;
use pocketmine\level\sound\GhastShootSound;

use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;

use kitpvp\uis\MainUi;
use kitpvp\kits\uis\{
	KitSelectUi
};
use kitpvp\arena\envoys\pickups\{
	EffectPickup,
	FreePlay,
	TechitCluster
};
use kitpvp\arena\spectate\uis\CompassUi;

use core\Core;

class MainListener implements Listener{

	public $plugin;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
	}

	public function onCreate(PlayerCreationEvent $e){
		$e->setPlayerClass(KitPvPPlayer::class);
	}

	public function onJoin(PlayerJoinEvent $e){
		$player = $e->getPlayer();
		$player->teleport(...$this->plugin->getArena()->getSpawnPosition());

		$this->plugin->getArena()->onJoin($player);
		foreach($this->plugin->getArena()->getSpectate()->spectating as $name){
			$p = $this->plugin->getServer()->getPlayerExact($name);
			if($p instanceof Player) $p->despawnFrom($player);
		}
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

		$this->plugin->getArena()->onQuit($player);
		$this->plugin->getKits()->deleteSession($player);
		$this->plugin->getAchievements()->deleteSession($player);
		$duels = $this->plugin->getDuels();
		$duels->onQuit($player);

		unset($this->plugin->jump[$player->getName()]);
	}

	public function onInteract(PlayerInteractEvent $e){
		$player = $e->getPlayer();
		$block = $e->getBlock();
		if($block->getId() == 54) $e->setCancelled(true);

		$arena = $this->plugin->getArena();
		if($arena->inSpawn($player) && $block->getX() == 62 && $block->getY() == 59 && $block->getZ() == 143){
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

		$item = $e->getItem();
		if($arena->inArena($player) && $arena->getSpectate()->isSpectating($player)){
			switch($item->getId()){
				case Item::PAPER:
					//Spectator settings?
				break;
				case Item::COMPASS:
					$player->showModal(new CompassUi($player));
				break;
				case Item::NETHER_STAR:
					$player->showModal(new KitSelectUi($player));
				break;
				case Item::BED:
					$arena->exitArena($player);
				break;
			}
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

	public function onPickup(InventoryPickupItemEvent $e){
		$player = $e->getInventory()->getHolder();
		if($player instanceof Player){
			if($this->plugin->getArena()->getSpectate()->isSpectating($player)){
				$e->setCancelled(true);
				return;
			}
			$item = $e->getItem();
			if($item instanceof EffectPickup){
				$effect = $item->getEffect();
				$player->addEffect($effect);
			}
			if($item instanceof FreePlay){
				$type = $item->getFreePlayType();
				$count = $item->getCount();
				$this->plugin->getKits()->getSession($player)->addFreePlays($type, $count);
				$player->sendMessage(TextFormat::OBFUSCATED . "||" . TextFormat::RESET . " " . TextFormat::GRAY . "Picked up " . TextFormat::YELLOW . "x".$count." ".$type." Free Play".($count > 1 ? "s" : "").TextFormat::GRAY."! ".TextFormat::WHITE.TextFormat::OBFUSCATED."||");
			}
			if($item instanceof TechitCluster){
				$count = $item->getTechitWorth();
				$player->addTechits($count);
				$player->sendMessage(TextFormat::OBFUSCATED . "||" . TextFormat::RESET . " " . TextFormat::GRAY . "Picked up " . TextFormat::AQUA . $count." Techits".TextFormat::GRAY."! ".TextFormat::WHITE.TextFormat::OBFUSCATED."||");
			}
		}
	}

	public function onTransaction(InventoryTransactionEvent $e){
		$player = $e->getTransaction()->getSource();
		$arena = $this->plugin->getArena();
		if($arena->inArena($player) && $arena->getSpectate()->isSpectating($player)){
			$e->setCancelled(true);
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