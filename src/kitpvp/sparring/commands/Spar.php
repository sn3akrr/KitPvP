<?php namespace kitpvp\sparring\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\AtPlayer as Player;
use core\Core;

class Spar extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		if($sender->getRank() == "default"){
			$sender->sendMessage(TextFormat::AQUA."Sparring> ".TextFormat::RED."You must have a premium rank to use sparring targets! Purchase one at ".TextFormat::YELLOW."atpe.buycraft.net");
			return;
		}
		$sparring = KitPvP::getInstance()->getSparring();
		//$sender->sendMessage(TextFormat::AQUA."Sparring> ".TextFormat::RED."In the making!");
		//return;

		if($sparring->isSparring($sender)){
			$sender->sendMessage(TextFormat::AQUA."Sparring> ".TextFormat::RED."You are already in Spar Mode!");
			return;
		}
		$sparring->startSpar($sender);
		$sender->sendMessage(TextFormat::AQUA."Sparring> ".TextFormat::GREEN."You are now in Spar mode! Hit the sparring target as much as you can!");
	}

	public function getPlugin() : \pocketmine\plugin\Plugin{
		return $this->plugin;
	}

}