<?php

/**
 * Helper для математики
 */
class MathHelper {

    const E = 2.71828;

    const PERCENTS = 100;


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
     * @return int
     */
    public static function getRandom() : int {
        $min = 0;
        $max = self::PERCENTS;
        try {
            $randomValue = random_int($min, $max);
        } catch (Exception $e) {
            $randomValue = rand($min, $max);
        }
        return $randomValue;
    }

}