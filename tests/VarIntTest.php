<?php


namespace MikeReinders\RuneTerraPHP\Tests;

use MikeReinders\RuneTerraPHP\Exception\VarIntException;
use MikeReinders\RuneTerraPHP\VarInt;
use PHPUnit\Framework\TestCase;

class VarIntTest extends TestCase {

    public function testVarIntThrowsExceptionOnNegativeIntegers(): void {
        $this->expectException(VarIntException::class);

        VarInt::get(-1);
    }

    public function testVarIntSelftest(): void {
        for ($i = 0; $i <= 999; $i++) {
            $bytes = VarInt::get($i);

            $this->assertEquals(
                $i,
                VarInt::pop($bytes)
            );
        }
    }

}