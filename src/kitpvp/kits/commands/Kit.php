<?php namespace kitpvp\kits\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\AtPlayer as Player;
use core\Core;

class Kit extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		$kits = KitPvP::getInstance()->getKits();
		if($kits->hasKit($sender)){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."You already have a kit equipped!");
			return;
		}
		if(count($args) !== 1){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."Usage: /kit <name>");
			return;
		}
		$kit = $kits->getKit(strtolower($args[0]));
		if($kit->getName() == "invalid"){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."The kit specified does not exist!");
			return;
		}
		if(!$kit->hasRequiredRank($sender)){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."You must be at least rank ".strtoupper($kit->getRequiredRank())." to use this kit! Purchase this rank at ".TextFormat::YELLOW."buy.atpe.co");
			return;
		}
		if(!$kit->hasEnoughCurrency($sender)){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."You do not have enough Techits to equip this kit! (".$kit->getPrice().")");
			return;
		}
		if($kit->hasPlayerCooldown($sender)){
			$cooldown = $kit->getPlayerCooldown($sender);
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."This kit has a cooldown! You can equip it again in ".$cooldown." play".($cooldown > 1 ? "s" : "")."!");
			return;
		}
		if(isset($kits->confirm[$sender->getName()])){
			if($kits->confirm[$sender->getName()][0] == $kit->getName()){
				$kit->equip($sender);
				$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::GREEN."You equipped the ".$kit->getName()." kit!");
			}else{
				$kits->confirm[$sender->getName()] = [$kit->getName(),time()];
				$sender->sendMessage($kit->getVisualItemList());
				$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::GREEN."Tap again to equip the ".$kit->getName()." kit!");
			}
		}else{
			$kits->confirm[$sender->getName()] = [$kit->getName(),time()];
			$sender->sendMessage($kit->getVisualItemList());
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::GREEN."Tap again to equip the ".$kit->getName()." kit!");
		}
	}

	public function getPlugin() : \pocketmine\plugin\Plugin{
		return $this->plugin;
	}

}