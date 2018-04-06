<?php

/**
 * Helper для математики
 */
class MathHelper {

    const PERCENTS = 100;

    const RANDOM_CALCULATE_KOEF = 100;

    /**
     * @param int $n
     * @return int
     */
    public static function fact($n) : int {
        $res = 1;
        for ($i=1; $i<=$n; $i++) {
            $res*=$i;
        }
        return $res;
    }

    /**
     * @return float
     */
    public static function getRandom() : float {
        $min = 0;
        $max = self::PERCENTS*self::RANDOM_CALCULATE_KOEF;
        try {
            $randomValue = random_int($min, $max);
        } catch (Exception $e) {
            $randomValue = rand($min, $max);
        }
        return $randomValue/self::RANDOM_CALCULATE_KOEF;
    }

}