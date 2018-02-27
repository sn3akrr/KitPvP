<?php namespace kitpvp\techits;

/**
 * Area used for server-specific techit saving.
 * Probably looks bad but hey it works..
 */

use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\techits\commands\AddTechits;

use core\stats\User;

class Techits{

	public $plugin;
	public $sessions = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		foreach([
			"CREATE TABLE IF NOT EXISTS techits(xuid BIGINT(16) NOT NULL UNIQUE, techits INT NOT NULL DEFAULT '0')",
		] as $query) $plugin->database->query($query);

		$plugin->getServer()->getCommandMap()->register("addtechits", new AddTechits($plugin, "addtechits", "Give player techits"));
	}

	public function close(){
		foreach($this->sessions as $session) $session->save();
	}

	public function createSession($player, $log = true){
		$session = new Session($player);
		if($log) $this->sessions[$session->getUser()->getGamertag()] = $session;

		return $session;
	}

	public function getSession($player){
		if($player instanceof Player) $player = $player->getName();
		if($player instanceof User) $player = $player->getGamertag();
		
		return $this->sessions[$player] ?? $this->createSession($player);
	}

	public function deleteSession($player, $save = true){
		if($player instanceof Player) $player = $player->getName();

		if($save) $this->sessions[$player]->save();

		unset($this->sessions[$player]);
	}

}