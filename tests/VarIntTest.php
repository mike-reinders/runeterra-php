<?php


namespace MikeReinders\RuneTerraPHP\Tests;

use InvalidArgumentException;
use MikeReinders\RuneTerraPHP\VarInt;
use PHPUnit\Framework\TestCase;

class VarIntTest extends TestCase {

    public function testVarIntThrowsExceptionOnNegativeIntegers(): void {
        $this->expectException(InvalidArgumentException::class);

        VarInt::get(-1);
    }

    public function testVarintSelftest(): void {
        for ($i = 0; $i <= 999; $i++) {
            $bytes = VarInt::get($i);

            $this->assertEquals(
                $i,
                VarInt::pop($bytes)
            );
        }
    }

}