<?php namespace kitpvp\nuke;

use pocketmine\item\Item;
use pocketmine\entity\Item as ItemEntity;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;
use core\Core;

class Nuke{

	const WAITING = 0;
	const FOUND = 1;
	const COLLECT = 2;

	const SHUTDOWN_CODE_ITEM = 600;

	public $plugin;

	public $shutdown_codes;

	public $mode = self::WAITING;
	public $time;

	public $wait_time;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		$this->registerShutdownCodes();
		$this->setTime();
		$this->setWaitTime();

		$plugin->getServer()->getScheduler()->scheduleRepeatingTask(new NukeTick($plugin), 1);
	}

	public function registerShutdownCodes(){
		for($i = 0; $i <= 20; $i++){
			$this->shutdown_codes[] = mt_rand(1000,9999);
		}
	}

	public function getRandomShutdownCode(){
		return $this->shutdown_codes[mt_rand(0,count($this->shutdown_codes) - 1)];
	}

	public function getMode(){
		return $this->mode;
	}

	public function setMode($mode){
		$this->mode = $mode;
	}

	public function getTime(){
		return $this->time;
	}

	public function setTime(){
		$this->time = time();
	}

	public function getWaitTime(){
		return $this->wait_time;
	}

	public function setWaitTime(){
		$this->wait_time = mt_rand(600,750);
	}

	public function dropShutdownCodes(){
		for($i = 0; $i <= 2; $i++){
			$item = new ShutdownCode();
			$item->setCustomName(TextFormat::GRAY."Shutdown code: ".$this->getRandomShutdownCode());
			$level = $this->plugin->getServer()->getLevelByName("KitArena");
			$x = mt_rand(105,150);
			$y = mt_rand(75,80);
			$z = mt_rand(105,150);
			$pos = new Vector3($x,$y,$z);
			$level->dropItem($pos, $item);
		}
	}

	public function clearShutdownCodes(){
		foreach($this->plugin->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $entity){
				if($entity instanceof ItemEntity) $entity->close();
			}
		}
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			$count = 0;
			for($i = 0, $size = $player->getInventory()->getSize(); $i < $size; $i++){
				$item = $player->getInventory()->getItem($i);
				if(stristr($item->getName(), "shutdown") != false){
					$player->getInventory()->setItem($i, Item::get(0));
					$count += $item->getCount();
				}
			}
			$exp = $count - 10;
			if($exp > 0){
				$player->addGlobalExp($exp);
				$player->sendMessage(TextFormat::AQUA."Nuke> ".TextFormat::GREEN."Gained ".$exp."EXP for extra shutdown codes collected!");
			}
		}
	}

}