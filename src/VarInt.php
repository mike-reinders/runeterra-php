<?php

namespace MikeReinders\RuneTerraPHP;

use InvalidArgumentException;

/**
 * Class VarInt
 * @package MikeReinders\RuneTerraPHP
 */
final class VarInt {

    private const AllButMSB = 0x7f;
    private const JustMSB = 0x80;

    /**
     * @param string $bytes
     * @return int
     */
    public static function pop(string &$bytes): int {
        $result = 0;
        $currentShift = 0;
        $bytesPopped = 0;

        for ($i = 0; $i < strlen($bytes); $i++) {
            $bytesPopped++;
            $current = ord($bytes[$i]) & VarInt::AllButMSB;
            $result |= $current << $currentShift;

            if ((ord($bytes[$i]) & VarInt::JustMSB) !== VarInt::JustMSB) {
                $bytes = substr($bytes, $bytesPopped);
                return $result;
            }

            $currentShift += 7;
        }

        throw new InvalidArgumentException('Byte array did not contain valid varints.');
    }


    /**
     * @param int $value
     * @return string
     * @throws InvalidArgumentException when $value is a negative integer
     */
    public static function get(int $value): string {
        $buff = "\x0\x0\x0\x0\x0\x0\x0\x0\x0\x0";

        if ($value < 0) {
            throw new InvalidArgumentException('VarInt requires non-negative values');
        }

        $currentIndex = 0;
        if ($value == 0) return "\x0";

        while ($value != 0) {
            $byteVal = $value & VarInt::AllButMSB;
            $value >>= 7;

            if ($value != 0) {
                $byteVal |= VarInt::JustMSB;
            }
            $buff[$currentIndex++] = chr($byteVal);
        }

        return substr($buff, 0, $currentIndex);
    }

}