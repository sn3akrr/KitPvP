<?php namespace kitpvp\kits\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

use kitpvp\KitPvP;

use core\Core;

class AddKitPasses extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		$kits = KitPvP::getInstance()->getKits();
		if($sender instanceof Player){
			if($sender->getRank() != "owner"){
				$sender->sendMessage(TextFormat::RED."This command requires OWNER rank!");
				return false;
			}
		}
		if(count($args) != 2){
			$sender->sendMessage(TextFormat::RED."Usage: /addkitpasses <player> <amount>");
			return false;
		}
		$stats = Core::getInstance()->getStats();
		$name = array_shift($args);
		if(!$stats->hasStats($name)){
			$sender->sendMessage(TextFormat::RED."Player never seen!");
			return false;
		}
		$amount = array_shift($args);
		if((int) $amount <= 0){
			$sender->sendMessage(TextFormat::RED."Amount must be numeric!");
			return false;
		}
		$kits->addKitPasses($name, $amount);
		$sender->sendMessage(TextFormat::GREEN."Gave ".$name." ".$amount." kit passes!");
		return true;
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

}