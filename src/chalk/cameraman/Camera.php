<?php

/**
 * @author ChalkPE <amato0617@gmail.com>
 * @since 2015-06-21 16:10
 */

namespace chalk\cameraman;

use chalk\cameraman\movement\Movement;
use chalk\cameraman\task\CameraTask;
use pocketmine\level\Location;
use pocketmine\Player;

class Camera {
    /** @var Player */
    private $target;

    /** @var Movement[] */
    private $movements = [];

    /** @var number */
    private $slowness;

    /** @var int */
    private $taskId = -1;

    /** @var int */
    private $gamemode;

    /** @var Location */
    private $location;

    /**
     * @param Player $target
     * @param Movement[] $movements
     * @param number $slowness
     */
    function __construct(Player $target, array $movements, $slowness){
        $this->target = $target;
        $this->movements = $movements;
        $this->slowness = $slowness;
    }

    /**
     * @return Player
     */
    public function getTarget(){
        return $this->target;
    }

    /**
     * @return Movement[]
     */
    public function getMovements(){
        return $this->movements;
    }

    /**
     * @param int $index
     * @return Movement
     */
    public function getMovement($index){
        return $this->movements[$index];
    }

    /**
     * @return number
     */
    public function getSlowness(){
        return $this->slowness;
    }

    public function isRunning(){
        return $this->taskId !== -1;
    }

    public function start(){
        if(!$this->isRunning()){
	        Cameraman::getInstance()->sendMessage($this->getTarget(), "Travelling will start in a few seconds...");

            $this->location = $this->getTarget()->getLocation();
            $this->gamemode = $this->getTarget()->getGamemode();

            $this->getTarget()->setGamemode(Player::SPECTATOR);

	        $this->taskId = Cameraman::getInstance()->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new CameraTask($this), Cameraman::DELAY, 20 / Cameraman::TICKS_PER_SECOND)->getTaskId();
	        Cameraman::getInstance()->sendMessage($this->getTarget(), "Travelling started! (slowness: " . $this->getSlowness() . ")");
        }
    }

    public function stop(){
        if($this->isRunning()){
			Cameraman::getInstance()->getServer()->getScheduler()->cancelTask($this->taskId); $this->taskId = -1;

			$this->getTarget()->teleport($this->location);
			$this->getTarget()->setGamemode($this->gamemode);

			Cameraman::getInstance()->sendMessage($this->getTarget(), "Travelling finished!");
        }
    }
}