<?php namespace kitpvp\combat\special\other;

use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\utils\TextFormat;

use KitPvP\KitPvP;

class Spell{

	public $name;
	public $spell;

	public function __construct(string $name, $spell){
		$this->name = $name;
		$this->spell = $spell;
	}

	public function getName(){
		return $this->name;
	}

	public function getSpell(){
		return $this->spell;
	}

	public function cast(Player $witch, Player $victim){
		$spell = $this->getSpell();
		KitPvP::getInstance()->getCombat()->getSlay()->damageAs($witch, $victim, 2);
		KitPvP::getInstance()->getCombat()->getSlay()->strikeLightning($victim);
		if($spell instanceof Effect){
			$victim->addEffect($spell->setDuration(20 * 7));
		}else{
			switch($spell){
				case "burn":
					$victim->setOnFire(7);
				break;
			}
		}
		$witch->addTitle(TextFormat::OBFUSCATED.TextFormat::RED.TextFormat::BOLD."KKK",TextFormat::GOLD."You casted a ".$this->getName()."!", 5, 40, 5);
		$victim->addTitle(TextFormat::OBFUSCATED.TextFormat::RED.TextFormat::BOLD."KKK",TextFormat::GOLD.$witch->getName()." casted ".$this->getName()." on you!", 5, 40, 5);
	}

}