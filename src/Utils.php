<?php
/**
 * Created by PhpStorm.
 * User: sligh
 * Date: 2018/5/2
 * Time: 22:20
 */

namespace CoolQSDK;


class Utils
{



    /**
     * multiply
     * a,b should be numeric
     * @param $a string
     * @param $b string
     * @return string
     */
    public static function mul($a, $b)
    {
        $lenA = strlen($a);
        $lenB = strlen($b);

        $result = '0';
        for ($inxA = $lenA - 1; $inxA >= 0; --$inxA) {
            $re = '';
            for ($i = $inxA + 1; $i < $lenA; ++$i) {
                $re = "0" . $re;
            }
            $j = 0;
            for ($inxB = $lenB - 1; $inxB >= 0; --$inxB) {
                $mul = (int)$a[$inxA] * (int)$b[$inxB] + $j;
                if ($mul >= 10) {
                    $j = floor($mul / 10);
                    $mul = $mul - $j * 10;
                } else {
                    $j = 0;
                }
                $re = (string)$mul . $re;
            }
            if ($j > 0) $re = (string)$j . $re;
            $result = add($result, $re);
        }

        return $result;
    }

    /**
     * add
     * a,b should be numeric
     * @param $a string
     * @param $b string
     * @return string
     */
    public static function add($a, $b)
    {
        $lenA = strlen($a);
        $lenB = strlen($b);

        $j = 0;
        $re = '';
        for ($inxA = $lenA - 1, $inxB = $lenB - 1; ($inxA >= 0 || $inxB >= 0); --$inxA, --$inxB) {
            $itemA = ($inxA >= 0) ? (int)$a[$inxA] : 0;
            $itemB = ($inxB >= 0) ? (int)$b[$inxB] : 0;
            $sum = $itemA + $itemB + $j;
            if ($sum > 9) {
                $j = 1;
                $sum = $sum - 10;
            } else {
                $j = 0;
            }
            $re = (string)$sum . $re;
        }
        if ($j > 0) $re = (string)$j . $re;

        return $re;
    }
}