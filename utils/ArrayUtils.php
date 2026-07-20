<?php

namespace Utils;

class ArrayUtils {
    static function array_remove_index(array $arr, int $index) {
        if ($arr != null && $index < count($arr)) {
            $res = array();
            for($i=0; $i<count($arr); $i++) {
                if ($index == $i) {
                    continue;
                } 
                $res[] = $arr[$i];
            }
            return $res;
        }
        return $arr;
    }

}