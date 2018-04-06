<?php

include 'Team.php';
include 'MathHelper.php';


/**
 * Класс для прогназирования исхода матча с помощью распределения Пуассона
 *
 * Class MatchPrediction
 */
class MatchPrediction {

    const PRECISION = 0.1;

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
     * MatchPrediction constructor.
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(array $data)
    {
        $this->validateData($data);
        $this->data = $data;
    }


    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    private function validateData(array $data) {
        foreach ($data as $id => $dataItem) {
            if (!isset($dataItem['games'])) {
                throw new InvalidArgumentException("Games num is not set for team '{$id}'.");
            }
            if (!is_numeric($dataItem['games'])) {
                throw new InvalidArgumentException("Games num is not number for team '{$id}'.");
            }
            if ($dataItem['games']<0) {
                throw new InvalidArgumentException("Games num is negative for team '{$id}'.");
            }
            if (!isset($dataItem['goals']) || !isset($dataItem['goals']['scored'])  || !isset($dataItem['goals']['skiped'])) {
                throw new InvalidArgumentException("Goals info is not set for team '{$id}'.");
            }
            if (!is_numeric($dataItem['goals']['scored'])  || !is_numeric($dataItem['goals']['skiped'])) {
                throw new InvalidArgumentException("Goals info is not numbers for team '{$id}'.");
            }
            if ($dataItem['goals']['scored']<0  || $dataItem['goals']['skiped']<0) {
                throw new InvalidArgumentException("Goals info have to be positive for team '{$id}'.");
            }
        }
    }

    /**
     * @param int $teamId1
     * @param int $teamId2
     * @throws InvalidArgumentException
     * @return array
     */
    public function predicate(int $teamId1, int $teamId2) {

        $team1 = new Team($teamId1, $this->getTeamData($teamId1) );
        $team2 = new Team($teamId2, $this->getTeamData($teamId2) );

        $this->setAvgGoalsInChMMatch();

        return $this->calculate($team1, $team2);
    }


    /**
     * @param Team $team1
     * @param Team $team2
     * @return array
     */
    private function calculate(Team $team1, Team $team2) {
        $mu1 = $this->getMu($team1, $team2);
        $mu2 = $this->getMu($team2, $team1);

        $randomValue1 = MathHelper::getRandom();
        $randomValue2 = MathHelper::getRandom();

        $team1ProbabilitiesSum = $team2ProbabilitiesSum = 0;
        $goalsTeam1 = $goalsTeam2 = null;
        $m = 0;
        do {
            if (is_null($goalsTeam1)) {
                $goalsTeam1 = $this->handleNextGoalsNum($team1ProbabilitiesSum, $m, $mu1, $randomValue1);
            }
            if (is_null($goalsTeam2)) {
                $goalsTeam2 = $this->handleNextGoalsNum($team2ProbabilitiesSum, $m, $mu2, $randomValue2);
            }
            if (!is_null($goalsTeam1) && !is_null($goalsTeam2)) {
                return [$goalsTeam1, $goalsTeam2];
            }

            $m++;
        } while ((MathHelper::PERCENTS-$team1ProbabilitiesSum)>self::PRECISION || (MathHelper::PERCENTS-$team2ProbabilitiesSum)>self::PRECISION);

        //если вдруг попали в self::PRECISION пересчитаем все заново
        $this->calculate($team1, $team2);

    }

    /**
     * @param float $probabilitiesSum
     * @param int $m
     * @param float $mu
     * @param float $randomValue
     * @return int|null
     */
    private function handleNextGoalsNum(float &$probabilitiesSum, int $m, float $mu, float $randomValue) {
        $prevProbabilitiesSum = $probabilitiesSum;
        $probabilitiesSum += $this->getProbability($mu, $m)*MathHelper::PERCENTS;
        if ($randomValue >= $prevProbabilitiesSum && $randomValue < $probabilitiesSum) {
            return $m;
        }
        return null;
    }

    /**
     * @param float $mu
     * @param int $m
     * @return float
     */
    private function getProbability(float $mu, int $m) : float  {
        return round($mu**$m / MathHelper::fact($m) * M_E**(-$mu), 4);
    }

    /**
     * @return float
     */
    private function getMu(Team $team, Team $oponent) : float {
        return $team->getAttackKoef($this->avgGoalsInChMMatch) *
            $oponent->getDefenseKoef($this->avgGoalsInChMMatch) *
            $this->avgGoalsInChMMatch;
    }


    /**
     * @param int $teamId
     * @return array
     * @throws InvalidArgumentException
     */
    private function getTeamData(int $teamId) : array {
        if (!isset($this->data[$teamId])) {
            throw new InvalidArgumentException("Team id '{$teamId}' is incorrect.");
        }
        return $this->data[$teamId];
    }

    /**
     *
     */
    private function setAvgGoalsInChMMatch() {
        if (!empty($this->avgGoalsInChMMatch)) {
            return;
        }
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

}