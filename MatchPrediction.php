<?php

include 'Team.php';
include 'MathHelper.php';


class MatchPrediction {

    const PRECISION = 5;

    /**
     * исходные данные
     * @var array
     */
    private $data;

    /**
     * сколько в среденем голов за матч по сумме всех ЧМ
     * @var float
     */
    private $avgGoalsInChMMatch;

    /**
     * @var Team
     */
    private $team1;

    /**
     * @var Team
     */
    private $team2;

    /**
     * Прогноз. Сколько забъет команда 1
     * @var int
     */
    private $team1Goals;

    /**
     * Прогноз. Сколько забъет команда 2
     * @var int
     */
    private $team2Goals;

    /**
     * Список вероятностей забивания m голов для каждой из каманд
     * @var array
     */
    private $goalsProbabilityList = [];


    /**
     * MatchPrediction constructor.
     * @param array $data
     * @param int $teamId1
     * @param int $teamId2
     */
    public function __construct(array $data, int $teamId1, int $teamId2)
    {
        $this->data = $data;
        $this->team1 = new Team($teamId1, $this->getTeamData($teamId1));
        $this->team2 = new Team($teamId2, $this->getTeamData($teamId2));
    }

    /**
     *
     */
    public function execute() {
        $this->setAvgGoalsInChMMatch();
        $this->getProbabilityList();
        $this->team1Goals = $this->getRandomPrediction($this->team1);
        $this->team2Goals = $this->getRandomPrediction($this->team2);
    }

    /**
     * @param Team $team
     * @return int|string
     */
    private function getRandomPrediction(Team $team) {
        $randomValue = MathHelper::getRandom();

        $prevVal = $nextVal = 0;
        foreach ($this->goalsProbabilityList[$team->getId()] as $n => $val) {
            $nextVal += $val;
            if ($randomValue >= $prevVal && $randomValue < $nextVal) {
                return $n;
            }
            $prevVal += $val;
        }
        //на случай, если попали в const::PRECISION, сгенерим новое случайное число
        return $this->getRandomPrediction($team);
    }

    /**
     *
     */
    private function getProbabilityList() {
        $mu1 = $this->getMu1();
        $mu2 = $this->getMu2();
        echo "mu === " . $mu1 . '----' . $mu2 . "\n";
        $t1ProbabilitiesSum = $t2ProbabilitiesSum = 0;
        $m = 0;
        do {
            $this->goalsProbabilityList[$this->team1->getId()][$m] = $this->getProbability($mu1, $m)*MathHelper::PERCENTS;
            $t1ProbabilitiesSum += $this->goalsProbabilityList[$this->team1->getId()][$m];

            $this->goalsProbabilityList[$this->team2->getId()][$m] = $this->getProbability($mu2, $m)*MathHelper::PERCENTS;
            $t2ProbabilitiesSum += $this->goalsProbabilityList[$this->team2->getId()][$m];

            $m++;
        } while ((MathHelper::PERCENTS-$t1ProbabilitiesSum)>self::PRECISION || (MathHelper::PERCENTS-$t2ProbabilitiesSum)>self::PRECISION);
        var_dump($this->goalsProbabilityList);
    }

    /**
     * @param float $mu
     * @param int $m
     * @return float
     */
    private function getProbability(float $mu, int $m) : float  {
        return $mu**$m / MathHelper::fact($m) * MathHelper::E**(-$mu);
    }

    /**
     * @return float
     */
    private function getMu1() : float {
        return $this->team1->getAttackKoef($this->avgGoalsInChMMatch) *
            $this->team2->getDefenseKoef($this->avgGoalsInChMMatch) *
            $this->avgGoalsInChMMatch;
    }

    /**
     * @return float
     */
    private function getMu2() : float {
        return $this->team2->getAttackKoef($this->avgGoalsInChMMatch) *
            $this->team1->getDefenseKoef($this->avgGoalsInChMMatch) *
            $this->avgGoalsInChMMatch;
    }

    /**
     * @param int $teamId
     * @return array
     */
    private function getTeamData(int $teamId) : array {
        return $this->data[$teamId];
    }

    /**
     *
     */
    private function setAvgGoalsInChMMatch() {
        $gamesSum = 0;
        $scoredSum = 0;
        $skipedSum = 0;
        foreach ($this->data as $id => $info) {
            $gamesSum += $info['games'];
            $scoredSum += $info['goals']['scored'];
            $skipedSum += $info['goals']['skiped'];
        }
        $gamesSum /= count($this->data); //сколько игр в среднем сыграла одна команда
        //команды не всегда играли друг с другом и не на одном ЧМ.
        //количество забитых и пропущеных голов не совпадает! (также, как выиграных и проиграных игр)
        //приходится брать примерное количество забитых голов
        $avgGoals = ($scoredSum+$skipedSum)/2;  //сколько примерно было забито голов

        $this->avgGoalsInChMMatch = $avgGoals/count($this->data)/$gamesSum;
    }

    /**
     * @return mixed
     */
    public function getTeam1Goals()
    {
        return $this->team1Goals;
    }

    /**
     * @return mixed
     */
    public function getTeam2Goals()
    {
        return $this->team2Goals;
    }

}