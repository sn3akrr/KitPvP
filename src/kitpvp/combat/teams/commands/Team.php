<?php namespace kitpvp\combat\teams\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\AtPlayer as Player;
use core\Core;

class Team extends Command implements PluginIdentifiableCommand{

	public $plugin;

	public function __construct(KitPvP $plugin, $name, $description){
		$this->plugin = $plugin;
		parent::__construct($name,$description);
		$this->setPermission("kitpvp.perm");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		/*$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."In the making!");
		return;*/

		if(count($args) == 0 || count($args) > 2){
			$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."Usage: /team <disband:info> OR /team <request:accept> <name>");
			return;
		}
		if(count($args) == 1){
			switch(strtolower($args[0])){
				default:
					$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."Usage: /team <disband:info> OR /team <request:accept> <name>");
				break;
				case "disband":
					if(!$teams->inTeam($sender)){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."You are not in a team!");
						return;
					}
					$teams->disbandTeam($teams->getPlayerTeamUid($sender));
				break;
				case "info":
					if(!$teams->inTeam($sender)){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."You are not in a team!");
						return;
					}
					$uid = $teams->getPlayerTeamUid($sender);
					$kills = $teams->getTeamKills($uid);
					$deaths = $teams->getTeamDeaths($uid);
					$kdr = ($deaths == 0 ? "N/A" : round($kills / $deaths));
					$sender->sendMessage(TextFormat::YELLOW."Your team info:");
					$sender->sendMessage(TextFormat::AQUA."Kills: ".TextFormat::GREEN.$kills);
					$sender->sendMessage(TextFormat::AQUA."Deaths: ".TextFormat::GREEN.$deaths);
					$sender->sendMessage(TextFormat::AQUA."KDR: ".TextFormat::GREEN.$kdr);
				break;
			}
			return;
		}
		if(count($args) == 2){
			switch(strtolower($args[0])){
				default:
					$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."Usage: /team <disband:info> OR /team <request:accept> <name>");
				break;
				case "request":
					if($teams->inTeam($sender)){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."You are already in a team!");
						return;
					}
					$target = $this->plugin->getServer()->getPlayer(implode(" ", explode("-", $args[1])));
					if(!$target instanceof Player){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."Player not found!");
						return;
					}
					if($teams->inTeam($target)){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."This player is already in a team!");
						return;
					}
					if($teams->hasTeamRequestFrom($target, $sender)){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."You have already sent this player a team request!");
						return;
					}
					$teams->sendTeamRequest($sender, $target);
					$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::GREEN."Sent team request to ".$target->getName());
				break;
				case "accept":
					if($teams->inTeam($sender)){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."You are already in a team!");
						return;
					}
					$target = $this->plugin->getServer()->getPlayer(implode(" ", explode("-", $args[1])));
					if(!$target instanceof Player){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."You don't have a team request from this player!");
						return;
					}
					if(!$teams->hasTeamRequestFrom($sender, $target)){
						$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED."You do not have a team request from this player!");
						return;
					}
					$teams->acceptTeamRequest($target, $sender);
				break;
			}
		}
	}

	public function getPlugin() : \pocketmine\plugin\Plugin{
		return $this->plugin;
	}

}