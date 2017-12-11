<?php namespace kitpvp\leaderboard;

use pocketmine\utils\TextFormat;
use pocketmine\Player;

use kitpvp\KitPvP;

use core\Core;
use core\stats\User;

class Leaderboard{

	const TYPE_KILLS = 0;
	const TYPE_KDR = 1;
	const TYPE_STREAK = 2;

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
			"CREATE TABLE IF NOT EXISTS leaderboard(
				xuid BIGINT(16) NOT NULL UNIQUE,
				kills_weekly INT NOT NULL DEFAULT '0', kills_monthly INT NOT NULL DEFAULT '0', kills_alltime INT NOT NULL DEFAULT '0',
				deaths_weekly INT NOT NULL DEFAULT '0', deaths_monthly INT NOT NULL DEFAULT '0', deaths_alltime INT NOT NULL DEFAULT '0',
				streak_weekly INT NOT NULL DEFAULT '0', streak_monthly INT NOT NULL DEFAULT '0', streak_alltime INT NOT NULL DEFAULT '0'
			);"
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

		$statement = $this->database->prepare("SELECT xuid FROM leaderboard WHERE xuid=?");
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
			"INSERT INTO leaderboard(xuid) VALUES($xuid)",
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
			"UPDATE leaderbord SET kills_$date='0', deaths_$date='0', streak_$date='0'",
		] as $query) $this->database->query($query);
	}

	public function typeToName($type){
		if($type == self::TYPE_KILLS) return "Kills";
		if($type == self::TYPE_KDR) return "KDR";
		if($type == self::TYPE_STREAK) return "Streak";

		return "Unknown";
	}

	public function getType(Player $player){
		return $this->type[$player->getName()] ?? self::TYPE_KILLS;
	}

	public function setType(Player $player, $type){
		$this->type[$player->getName()] = $type;
	}

	public function generateText(Player $player, $date, $value = -1){
		$type = strtolower($this->typeToName($this->getType($player))) ?? "kills";
		if($type != "kdr"){
			if($value == -1){
				$xuid = $player->getXboxData("XUID");
				$statement = $this->database->prepare("SELECT ".$type."_$date FROM leaderboard WHERE xuid=?");
				$statement->bind_param("i", $xuid);
				$statement->bind_result($val);
				if($statement->execute()){
					$statement->fetch();
				}
				if($val == null) $val = 0;
				$text = TextFormat::GREEN."YOU: ".TextFormat::AQUA.$player->getName()." ".TextFormat::YELLOW.$val;
				return $text;
			}

			$text = TextFormat::GREEN.$value.". ".TextFormat::RED."No stats found!";
			if($statement = $this->database->query("SELECT xuid, ".$type."_$date FROM leaderboard ORDER BY ".$type."_$date DESC LIMIT 5")){
				$key = 1;
				while($array = $statement->fetch_array()){
					if($key == $value){
						$name = Core::getInstance()->getNetwork()->xuidToGamertag($array["xuid"]);
						$text = TextFormat::GREEN.$value.". ".TextFormat::AQUA.$name.TextFormat::YELLOW." ".$array[$type."_".$date];
					}
					$key++;
				}
			}
			return $text;
		}
		if($value == -1){
			$xuid = $player->getXboxData("XUID");
			$statement = $this->database->prepare("SELECT kills_$date / deaths_$date FROM leaderboard WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->bind_result($val);
			if($statement->execute()){
				$statement->fetch();
			}
			if($val == null) $val = 0;
			$text = TextFormat::GREEN."YOU: ".TextFormat::AQUA.$player->getName()." ".TextFormat::YELLOW.$val;
			return $text;
		}

		$text = TextFormat::GREEN.$value.". ".TextFormat::RED."No stats found!";
		if($statement = $this->database->query("SELECT xuid, (kills_$date / deaths_$date) as kdr FROM leaderboard ORDER BY kills_$date / deaths_$date DESC LIMIT 5")){
			$key = 1;
			while($array = $statement->fetch_array()){
				if($key == $value){
					$name = Core::getInstance()->getNetwork()->xuidToGamertag($array["xuid"]);
					$text = TextFormat::GREEN.$value.". ".TextFormat::AQUA.$name.TextFormat::YELLOW." ".($array["kdr"] ?? 0);
				}
				$key++;
			}
		}
		return $text;
	}

	public function addKill($player){
		$xuid = (new User($player))->getXuid();

		$dates = ["weekly","monthly","alltime"];
		foreach($dates as $date){
			$statement = $this->database->prepare("UPDATE leaderboard SET kills_$date = kills_$date + 1 WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->execute();
			$statement->close();
		}
	}

	public function addDeath($player){
		$xuid = (new User($player))->getXuid();

		$dates = ["weekly","monthly","alltime"];
		foreach($dates as $date){
			$statement = $this->database->prepare("UPDATE leaderboard SET deaths_$date = deaths_$date + 1 WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->execute();
			$statement->close();
		}
	}

	public function setStreak($player, $streak){
		$xuid = (new User($player))->getXuid();

		$dates = ["weekly","monthly","alltime"];
		foreach($dates as $date){
			$statement = $this->database->prepare("UPDATE leaderboard SET streak_$date = $streak WHERE xuid=?");
			$statement->bind_param("i", $xuid);
			$statement->execute();
			$statement->close();
		}
	}

	public function getKills($player, $date = "alltime"){
		$xuid = (new User($player))->getXuid();

		$statement = $this->database->prepare("SELECT kills_$date FROM leaderboard WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($kills);
		if($statement->execute()){
			$statement->fetch();
		}
		return $kills ?? 0;
	}

	public function getDeaths($player, $date = "alltime"){
		$xuid = (new User($player))->getXuid();

		$statement = $this->database->prepare("SELECT deaths_$date FROM leaderboard WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($deaths);
		if($statement->execute()){
			$statement->fetch();
		}
		return $deaths ?? 0;
	}

	public function getKdr($player, $date = "alltime"){
		$xuid = (new User($player))->getXuid();

		$statement = $this->database->prepare("SELECT (kills_$date / deaths_$date) as kdr FROM leaderboard WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($kdr);
		if($statement->execute()){
			$statement->fetch();
		}
		return $kdr ?? 0;
	}

	public function getStreak($player, $date = "alltime"){
		$xuid = ($player instanceof Player ? $player->getXboxData("XUID") : Core::getInstance()->getNetwork()->gamertagToXuid($player));

		$statement = $this->database->prepare("SELECT streak_$date FROM leaderboard WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($streak);
		if($statement->execute()){
			$statement->fetch();
		}
		return $streak ?? 0;
	}

}