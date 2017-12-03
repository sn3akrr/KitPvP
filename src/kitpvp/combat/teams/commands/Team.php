<?php namespace kitpvp\combat\teams\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\combat\teams\uis\{
	inteam\InTeamMainUi,
	noteam\NoTeamMainUi
};

class Team extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		if($teams->inTeam($sender)){
			$player->showModal(new InTeamMainUi($player));
		}else{
			$player->showModal(new NoTeamMainUi($player));
		}
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

}