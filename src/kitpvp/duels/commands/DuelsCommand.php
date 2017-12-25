<?php namespace kitpvp\duels\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\duels\uis\QueueSelectUi;

use core\Core;

class DuelsCommand extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		$duels = KitPvP::getInstance()->getDuels();
		if($duels->inDuel($sender)){
			$sender->sendMessage(TextFormat::RED . "You cannot use this menu while in a duel!");
			return false;
		}
		$kits = $this->plugin->getKits();
		$session = $kits->getSession($sender);
		if(!$session->hasKit()){
			$sender->sendMessage(TextFormat::RED . "You cannot use this menu without a kit!");
			return false;
		}
		if($this->plugin->getArena()->inArena($sender)){
			$sender->sendMessage(TextFormat::RED . "You cannot use this menu while in the arena!");
			return false;
		}
		$sender->showModal(new QueueSelectUi($sender));
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

}