<?php

/**
 * Футбольная команда
 * Class Team
 */
class Team{

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $gamesNum;

    /**
     * @var int
     */
    private $goalsScored;

    /**
     * @var int
     */
    private $goalsSkiped;


    /**
     * Team constructor.
     * @param int $id
     * @param array $data
     */
    public function __construct(int $id, array $data) {
        $this->setId($id);
        $this->setGamesNum($data['games']);
        $this->setGoalsScored($data['goals']['scored']);
        $this->setGoalsSkiped($data['goals']['skiped']);
    }

    /**
     * @param float $chmAvgGoals
     * @return float
     */
    public function getAttackKoef(float $chmAvgGoals) : float  {
        if ($chmAvgGoals == 0) return 0;
        return $this->getAverageScoredForGame() / $chmAvgGoals;
    }

    /**
     * @param float $chmAvgGoals
     * @return float
     */
    public function getDefenseKoef(float $chmAvgGoals) : float  {
        if ($chmAvgGoals == 0) return 0;
        return $this->getAverageSkipedForGame() / $chmAvgGoals;
    }

    /**
     * @return float
     */
    private function getAverageScoredForGame() : float  {
        return $this->getGoalsScored() / $this->getGamesNum();
    }

    /**
     * @return float
     */
    private function getAverageSkipedForGame() : float  {
        return $this->getGoalsSkiped() / $this->getGamesNum();
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getGamesNum(): int
    {
        return $this->gamesNum;
    }

    /**
     * @param int $gamesNum
     */
    public function setGamesNum(int $gamesNum)
    {
        $this->gamesNum = $gamesNum;
    }

    /**
     * @return int
     */
    public function getGoalsScored(): int
    {
        return $this->goalsScored;
    }

    /**
     * @param int $goalsScored
     */
    public function setGoalsScored(int $goalsScored)
    {
        $this->goalsScored = $goalsScored;
    }

    /**
     * @return int
     */
    public function getGoalsSkiped(): int
    {
        return $this->goalsSkiped;
    }

    /**
     * @param int $goalsSkiped
     */
    public function setGoalsSkiped(int $goalsSkiped)
    {
        $this->goalsSkiped = $goalsSkiped;
    }

}