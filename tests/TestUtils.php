<?php


namespace MikeReinders\RuneTerraPHP\Tests;


use Base32\Base32;

final class TestUtils
{

    /**
     * @return array
     */
    public static function getDeckCodeTestData(): array {
        return array_merge(
            require(__DIR__.'/data/DeckCodesTestData.php'),
            require(__DIR__.'/data/RiotGamesTestData.php')
        );
    }

    public static function removeVersionFromDeckCode(string $deck_code): string {
        return Base32::encode(substr(Base32::decode($deck_code), 1));
    }

}