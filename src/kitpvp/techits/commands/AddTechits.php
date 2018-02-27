<?php namespace kitpvp\techits\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;

use pocketmine\utils\TextFormat;
use pocketmine\Player;

use kitpvp\KitPvP;

use core\Core;

class AddTechits extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		if($sender instanceof Player){
			if($sender->getRank() != "owner"){
				$sender->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You cannot use this command.");
				return false;
			}
		}
		if(count($args) != 2){
			$sender->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Usage: /addtechits <player> <amount>");
			return false;
		}
		$stats = Core::getInstance()->getStats();

		$name = array_shift($args);
		$amount = (int) array_shift($args);

		$player = $this->plugin->getServer()->getPlayerExact($name);
		if(!$player instanceof Player){
			if(!$stats->hasStats($name)){
				$sender->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Player never seen on AvengeTech!");
				return false;
			}
		}

		if($amount <= 0 || $amount > 100000000){
			$sender->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Amount must be between 0 and 100,000,000!");
			return false;
		}

		if($player instanceof Player){
			$player->addTechits($amount);
			$player->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::GRAY . "You have earned " . TextFormat::AQUA . $amount . " Techits" . TextFormat::GRAY . "!");
		}else{
			$session = $this->plugin->getTechits()->createSession($name, false);
			$session->addTechits($amount);
			$session->save();
		}

		$sender->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Successfully gave " . TextFormat::YELLOW . $name . TextFormat::AQUA . " " . $amount . " Techits" . TextFormat::GRAY."!");
		return true;
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

}