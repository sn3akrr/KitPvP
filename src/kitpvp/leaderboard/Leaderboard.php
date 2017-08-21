<?php namespace kitpvp\leaderboard;

use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\Core;
use core\AtPlayer as Player;

class Leaderboard{

	const TYPE_KILLS = 0;
	const TYPE_DEATHS = 1;
	const TYPE_ASSISTS = 2;
	const TYPE_REVENGE = 3;

	const DATE_WEEKLY = "weekly";
	const DATE_MONTHLY = "monthly";
	const DATE_ALLTIME = "alltime";

	public $plugin;
	public $database;

	public $type = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		$this->database = $plugin->database;
		foreach([
			"CREATE TABLE IF NOT EXISTS leaderboard_kills(xuid BIGINT(16) NOT NULL UNIQUE, weekly INT NOT NULL, monthly INT NOT NULL, alltime INT NOT NULL);",
			"CREATE TABLE IF NOT EXISTS leaderboard_deaths(xuid BIGINT(16) NOT NULL UNIQUE, weekly INT NOT NULL, monthly INT NOT NULL, alltime INT NOT NULL);",
			"CREATE TABLE IF NOT EXISTS leaderboard_assists(xuid BIGINT(16) NOT NULL UNIQUE, weekly INT NOT NULL, monthly INT NOT NULL, alltime INT NOT NULL);",
			"CREATE TABLE IF NOT EXISTS leaderboard_revenge(xuid BIGINT(16) NOT NULL UNIQUE, weekly INT NOT NULL, monthly INT NOT NULL, alltime INT NOT NULL);",
		] as $query) $this->database->query($query);

		$dd = $plugin->dir . "lb.cache";
		if(!file_exists($dd)){
			$day = strtolower(date("l", strtotime(date("y-m-d"))));
			$month = date("m");
			file_put_contents($dd, $day."\n".$month);
		}
		$this->parseCache(file_get_contents($dd));
	}

	public function hasStats(Player $player){
		$xuid = $player->getXboxData("XUID");

		$statement = $this->database->prepare("SELECT xuid FROM leaderboard_kills WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($exists);
		if($statement->execute()){
			$statement->fetch();
		}
		return (int) $exists > 1;		
	}

	public function newStats(Player $player){
		$xuid = $player->getXboxData("XUID");
		$z = 0;
		foreach([
			"INSERT INTO leaderboard_kills(xuid, weekly, monthly, alltime) VALUES($xuid, $z, $z, $z)",
			"INSERT INTO leaderboard_deaths(xuid, weekly, monthly, alltime) VALUES($xuid, $z, $z, $z)",
			"INSERT INTO leaderboard_assists(xuid, weekly, monthly, alltime) VALUES($xuid, $z, $z, $z)",
			"INSERT INTO leaderboard_revenge(xuid, weekly, monthly, alltime) VALUES($xuid, $z, $z, $z)",
		] as $query) $this->database->query($query);
	}

	public function parseCache($contents){
		$current_day = strtolower(date("l", strtotime(date("y-m-d"))));
		$current_month = date("m");

		$array = explode("\n", $contents);

		$day = $array[0];
		$month = $array[1];

		if($current_day != $day){
			if($current_day == "sunday"){
				$this->reset("weekly");
			}
		}
		if($current_month != $month){
			$this->reset("monthly");
		}
		file_put_contents($this->plugin->dir . "lb.cache", $current_day."\n".$current_month);
	}

	public function reset($date){
		foreach([
			"UPDATE kills SET $date='0'",
			"UPDATE deaths SET $date='0'",
			"UPDATE assists SET $date='0'",
			"UPDATE revenge SET $date='0'",
		] as $query) $this->database->query($query);
	}

	public function typeToName($type){
		if($type == self::TYPE_KILLS) return "Kills";
		if($type == self::TYPE_DEATHS) return "Deaths";
		if($type == self::TYPE_ASSISTS) return "Assists";
		if($type == self::TYPE_REVENGE) return "Revenge";
	}

	public function getType(Player $player){
		return $this->type[$player->getName()] ?? self::TYPE_KILLS;
	}

	public function setType(Player $player, $type){
		$this->type[$player->getName()] = $type;
	}

	public function generateText(Player $player, $date, $value = -1){
		$type = strtolower($this->typeToName($this->getType($player)));
		if($value == -1){
			$xuid = $player->getXboxData("XUID");
			$statement = $this->database->prepare("SELECT $date FROM leaderboard_$type WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->bind_result($val);
			if($statement->execute()){
				$statement->fetch();
			}
			if($val == null) $val = 0;
			$text = TextFormat::GREEN."YOU: ".TextFormat::AQUA.$player->getName()." ".TextFormat::YELLOW.$val;
			return $text;
		}

		if($statement = $this->database->query("SELECT xuid, $date FROM leaderboard_$type ORDER BY $date DESC LIMIT 5")){
			$key = 1;
			while($array = $statement->fetch_array()){
				if($key == $value){
					$name = Core::getInstance()->getNetwork()->xuidToGamertag($array["xuid"]);
					$text = TextFormat::GREEN.$value.". ".TextFormat::AQUA.$name.TextFormat::YELLOW." ".$array[$date];
				}
				$key++;
			}
		}else{
			$text = TextFormat::GREEN.$value.". ".TextFormat::RED."No stats found!";
		}
		return $text;
	}

	public function addKill(Player $player){
		$xuid = $player->getXboxData("XUID");

		$dates = ["weekly","monthly","alltime"];
		foreach($dates as $date){
			$statement = $this->database->prepare("UPDATE leaderboard_kills SET $date = $date + 1 WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->execute();
			$statement->close();
		}
	}

	public function addDeath(Player $player){
		$xuid = $player->getXboxData("XUID");

		$dates = ["weekly","monthly","alltime"];
		foreach($dates as $date){
			$statement = $this->database->prepare("UPDATE leaderboard_deaths SET $date = $date + 1 WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->execute();
			$statement->close();
		}
	}

	public function addAssist(Player $player){
		$xuid = $player->getXboxData("XUID");

		$dates = ["weekly","monthly","alltime"];
		foreach($dates as $date){
			$statement = $this->database->prepare("UPDATE leaderboard_assists SET $date = $date + 1 WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->execute();
			$statement->close();
		}
	}

	public function addRevenge(Player $player){
		$xuid = $player->getXboxData("XUID");

		$dates = ["weekly","monthly","alltime"];
		foreach($dates as $date){
			$statement = $this->database->prepare("UPDATE leaderboard_revenge SET $date = $date + 1 WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->execute();
			$statement->close();
		}
	}

	public function getKills($player, $date){
		$xuid = ($player instanceof Player ? $player->getXboxData("XUID") : Core::getInstance()->getNetwork()->gamertagToXuid($player));

		$statement = $this->database->prepare("SELECT $date FROM leaderboard_kills WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($kills);
		if($statement->execute()){
			$statement->fetch();
		}
		return $kills ?? 0;
	}

	public function getDeaths($player, $date){
		$xuid = ($player instanceof Player ? $player->getXboxData("XUID") : Core::getInstance()->getNetwork()->gamertagToXuid($player));

		$statement = $this->database->prepare("SELECT $date FROM leaderboard_deaths WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($deaths);
		if($statement->execute()){
			$statement->fetch();
		}
		return $deaths ?? 0;
	}

	public function getAssists($player, $date){
		$xuid = ($player instanceof Player ? $player->getXboxData("XUID") : Core::getInstance()->getNetwork()->gamertagToXuid($player));

		$statement = $this->database->prepare("SELECT $date FROM leaderboard_assists WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($assists);
		if($statement->execute()){
			$statement->fetch();
		}
		return $assists ?? 0;
	}

	public function getRevenge($player, $date){
		$xuid = ($player instanceof Player ? $player->getXboxData("XUID") : Core::getInstance()->getNetwork()->gamertagToXuid($player));

		$statement = $this->database->prepare("SELECT $date FROM leaderboard_revenge WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($revenge);
		if($statement->execute()){
			$statement->fetch();
		}
		return $revenge ?? 0;
	}

}