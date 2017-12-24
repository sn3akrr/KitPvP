<?php namespace kitpvp\kits\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\kits\uis\KitConfirmUi;

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
		$session = $kits->getSession($sender);
		if($session->hasKit()){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."You already have a kit equipped!");
			return false;
		}
		if(count($args) !== 1){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."Usage: /kit <name>");
			return false;
		}
		$kit = $kits->getKit(strtolower($args[0]));
		if($kit->getName() == "invalid"){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."The kit specified does not exist!");
			return false;
		}
		if(!$kit->hasRequiredRank($sender)){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."You must be at least rank ".strtoupper($kit->getRequiredRank())." to use this kit! Purchase this rank at ".TextFormat::YELLOW."buy.atpe.co");
			return false;
		}
		if(!$kit->hasEnoughCurrency($sender)){
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."You do not have enough Techits to equip this kit! (".$kit->getPrice().")");
			return false;
		}
		if($kit->hasPlayerCooldown($sender)){
			$cooldown = $kit->getPlayerCooldown($sender);
			$sender->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::RED."This kit has a cooldown! You can equip it again in ".$cooldown." play".($cooldown > 1 ? "s" : "")."!");
			return false;
		}
		$sender->showModal(new KitConfirmUi($kit, $sender));
		return true;
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

}