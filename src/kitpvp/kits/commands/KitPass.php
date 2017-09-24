<?php namespace kitpvp\kits\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

use kitpvp\KitPvP;

use core\network\Links;
use core\Core;

class KitPass extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		$kits = KitPvP::getInstance()->getKits();
		$passes = $kits->getKitPasses($sender);
		if($passes <= 0){
			$sender->sendMessage(TextFormat::AQUA."KitPass> ".TextFormat::RED."You do not have any kit passes! Earn them by voting, or purchase them at ".TextFormat::YELLOW.Links::SHOP);
			return false;
		}
		if($kits->hasPassCooldown($sender)){
			$sender->sendMessage(TextFormat::AQUA."KitPass> ".TextFormat::RED."You have a kit pass cooldown! You can use another kit pass after a few plays.");
			return false;
		}
		if($kits->hasKitPassActive($sender)){
			$sender->sendMessage(TextFormat::AQUA."KitPass> ".TextFormat::GREEN."Current kit pass usage was cancelled.");
		}else{
			$sender->sendMessage(TextFormat::AQUA."KitPass> ".TextFormat::GREEN."Kit pass activated! The next kit you equip will be FREE!");
		}
		$kits->toggleKitPass($sender);
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

}