<?php

namespace MikeReinders\RuneTerraPHP;

use InvalidArgumentException;

/**
 * Class VarInt
 * @package MikeReinders\RuneTerraPHP
 */
final class VarInt {

    private const AllButMSB = 0x7F;
    private const JustMSB = 0x80;

    /**
     * @param string $bytes
     * @param int $offset
     * @param int $bytesPopped
     * @return int
     */
    public static function pop(string $bytes, int $offset, int &$bytesPopped): int {
        $result = 0;

        for ($i = $offset, $m = strlen($bytes); $i < $m; $i++) {
            $byte = ord($bytes[$i]);

            $result |= ($byte & VarInt::AllButMSB) << ($i * 7);

            if (($byte & VarInt::JustMSB) != VarInt::JustMSB) {
                $bytesPopped = $i;
                return $result;
            }
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