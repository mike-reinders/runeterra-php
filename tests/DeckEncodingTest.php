<?php

namespace MikeReinders\RuneTerraPHP\Tests;

use Base32\Base32;
use MikeReinders\RuneTerraPHP\DeckEncodingFactory;
use PHPUnit\Framework\TestCase;

final class DeckEncodingTest extends TestCase  {

    /**
     * @return array
     */
    private function getDeckCodesTestData(): array {
        return array_merge(
            require(__DIR__.'/DeckCodesTestData.php'),
            require(__DIR__.'/RiotGamesTestData.php')
        );
    }

    public function testDeckEncodingSelfTestAndTestDataFailTest(): void
    {
        $previousExpectedDeck = null;
        foreach ($this->getDeckCodesTestData() as $deckCode => $expectedDeck) {

            if ($previousExpectedDeck !== null) {
                $encodedDeck = DeckEncodingFactory::toCardCodeDeck($deckCode);

                $this->assertNotEquals(
                    $previousExpectedDeck,
                    $encodedDeck
                );
            }

            $previousExpectedDeck = $expectedDeck;
        }
    }

    public function testDeckEncodingSelfTestAndTestDataTest(): void
    {
        foreach ($this->getDeckCodesTestData() as $deck_code => $expected_deck) {
            $encodedDeck = DeckEncodingFactory::toCardCodeDeck($deck_code);

            $this->assertEqualsCanonicalizing(
                $expected_deck,
                $encodedDeck,
                'Failed to verify Deck-Equality for DeckCode:'.$deck_code
            );

            $this->assertEquals(
                Base32::encode(substr(Base32::decode($deck_code), 1)),
                Base32::encode(substr(Base32::decode(DeckEncodingFactory::fromCardCodeDeck($encodedDeck)), 1)),
                'Failed to verify DeckCode-Equality for DeckCode:'.$deck_code
            );
        }
    }

}