<?php namespace kitpvp\achievements\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\achievements\uis\ListUI;

use core\Core;

class AchCommand extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		$a = KitPvP::getInstance()->getAchievements();
		$sender->showModal(new ListUI($a->getSession($sender)));
		return true;
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

}