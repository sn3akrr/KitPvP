<?php namespace kitpvp\kits\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\AtPlayer as Player;

class ReplenishKit extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
		$this->setAliases(["restorekit","rkit","rk"]);
	}

	public function execute(CommandSender $sender, string $label, array $args){
		$kits = KitPvP::getInstance()->getKits();
		if(!$kits->hasKit($sender)){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."You don't have a kit to replenish!");
			return;
		}
		$kits->getPlayerKit($sender)->replenish($sender);
		$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::GREEN."You replenished your kit!");
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

}