<?php namespace kitpvp\achievements;

class Achievement{

	public $id;

	public $obtained = null;

	public function __construct($id){
		$this->id = $id;
	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return AchievementList::ACHIEVEMENTS[$this->getId()]["displayName"];
	}

	public function getDescription(){
		return AchievementList::ACHIEVEMENTS[$this->getId()]["description"];
	}

	public function isDescriptionHidden(){
		return AchievementList::ACHIEVEMENTS[$this->getId()]["hidden"] ?? false;
	}

	public function getPoints(){
		return AchievementList::ACHIEVEMENTS[$this->getId()]["points"];
	}

	public function hasIcon(){
		return $this->getIcon() != null;
	}

	public function getIcon(){
		return AchievementList::ACHIEVEMENTS[$this->getId()]["icon"] ?? null;
	}

	public function isObtained(){
		return $this->obtained == null ? false : true;
	}

	public function setObtained(){
		$this->obtained = time();
	}

	public function getObtained(){
		return $this->obtained;
	}

	public function getFormattedObtained(){
		return gmdate("m/d/y", $this->getObtained());
	}

}