<?php namespace kitpvp\achievements\uis;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

use kitpvp\KitPvP;
use kitpvp\achievements\{
	Session,
	Achievement
};

class AchDetailUI extends SimpleForm{

	public $session;
	public $self;
	public $page;

	public $achievement;

	public function __construct(Session $session, Achievement $achievement, $self = true, $page){
		$this->session = $session;
		$this->self = $self; //TODO: Implement
		$this->page = $page;

		$this->achievement = $achievement;

		parent::__construct($achievement->getName(), "Description: " . (($achievement->isDescriptionHidden() && !$session->hasAchievement($achievement->getId())) ? TextFormat::OBFUSCATED : "") . $achievement->getDescription() . TextFormat::RESET . PHP_EOL . PHP_EOL . "Points worth: " . $achievement->getPoints() . PHP_EOL . PHP_EOL . ($session->hasAchievement($achievement->getId()) ? "This achievement was obtained on " . $session->getAchievement($achievement->getId())->getFormattedObtained() : "This achievement is locked."));

		$this->addButton(new Button("Go back"));
	}

	public function handle($response, Player $player){
		$player->showModal(new ListUI($this->session, $this->self, $this->page));
	}

}