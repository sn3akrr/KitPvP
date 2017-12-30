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
	public $page;

	public $hasNext = false;
	public $hasBack = false;

	public $achievements;

	public function __construct(Session $session, $self = true, $page = 1){
		$this->session = $session;
		$this->self = $self; //TODO: Implement
		$this->page = $page;

		$a = KitPvP::getInstance()->getAchievements();

		parent::__construct("Achievements (" . $page . "/" . $a->getTotalPages() . ")", "You have " . $session->getPoints() . " achievement points and " . $session->getAchievementCount() . "/" . $a->getAchievementCount() . " achievements unlocked." . PHP_EOL . PHP_EOL . "Tap an achievement for more details!");
		$key = 0;
		foreach($a->getAchievements($page) as $key => $ac){
			$this->achievements[$key] = $ac;
			$key++;
			$id = $ac->getId();
			if($session->hasAchievement($id)){
				$aaa = $session->getAchievement($id);
				$obtained = $aaa->getFormattedObtained();
				$this->addButton(new Button(TextFormat::DARK_GREEN . wordwrap($ac->getName(), 30, PHP_EOL) . PHP_EOL . TextFormat::DARK_GRAY . TextFormat::ITALIC . "Obtained " . $obtained));
			}else{
				$this->addButton(new Button(TextFormat::RED . wordwrap($ac->getName(), 30, PHP_EOL)));
			}
		}
		if($a->hasBackPage($page)){
			$this->hasBack = true;
			$this->addButton(new Button("Previous Page (" . ($page - 1) . "/" . $a->getTotalPages() . ")"));
		}
		if($a->hasNextPage($page)){
			$this->hasNext = true;
			$this->addButton(new Button("Next Page (" . ($page + 1) . "/" . $a->getTotalPages() . ")"));
		}
	}

	public function handle($response, Player $player){
		foreach($this->achievements as $key => $achievement){
			if($key == $response){
				$player->showModal(new AchDetailUI($this->session, $achievement, $this->self, $this->page));
				return;
			}
		}
		if($this->hasBack && $this->hasNext){
			if($response == count($this->achievements)){
				$player->showModal(new ListUI($this->session, $this->self, $this->page - 1));
				return;
			}
			if($response == count($this->achievements) + 1){
				$player->showModal(new ListUI($this->session, $this->self, $this->page + 1));
				return;
			}
			return;
		}
		if($this->hasBack){
			if($response == count($this->achievements)){
				$player->showModal(new ListUI($this->session, $this->self, $this->page - 1));
			}
			return;
		}
		if($this->hasNext){
			if($response == count($this->achievements)){
				$player->showModal(new ListUI($this->session, $this->self, $this->page + 1));
			}
			return;
		}
	}

}