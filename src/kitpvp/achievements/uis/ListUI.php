<?php namespace kitpvp\achievements\uis;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

use kitpvp\KitPvP;
use kitpvp\achievements\Session;

class ListUI extends SimpleForm{

	public $session;
	public $self;

	public $achievements;

	public function __construct(Session $session, $self = true){
		$this->session = $session;
		$this->self = $self; //TODO: Implement
		$a = KitPvP::getInstance()->getAchievements();

		parent::__construct("Achievements", "Here is a full list of achievements. Collect them all for a prize!" . PHP_EOL . PHP_EOL . "You have " . $session->getPoints() . " achievement points and " . $session->getAchievementCount() . "/" . $a->getAchievementCount() . " achievements unlocked.");
		$key = 0;
		foreach($a->getAchievements() as $id => $ac){
			$this->achievements[$key] = $ac;
			$key++;
			if($session->hasAchievement($id)){
				$aaa = $session->getAchievement($id);
				$obtained = $aaa->getFormattedObtained();
				$this->addButton(new Button(TextFormat::DARK_GREEN . $aaa->getName() . PHP_EOL . TextFormat::DARK_GRAY . TextFormat::ITALIC . "Obtained " . $obtained));
			}else{
				$this->addButton(new Button(TextFormat::RED . $ac->getName()));
			}
		}
	}

	public function handle($response, Player $player){
		foreach($this->achievements as $key => $achievement){
			if($key == $response){
				//$player->showModal(new AchDetailUI($this->session, $achievement, $this->self));
				return;
			}
		}
	}

}