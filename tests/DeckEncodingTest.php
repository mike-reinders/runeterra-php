<?php

namespace MikeReinders\RuneTerraPHP\Tests;

use Base32\Base32;
use Exception;
use MikeReinders\RuneTerraPHP\DeckEncoding;
use PHPUnit\Framework\TestCase;

final class DeckEncodingTest extends TestCase  {

    /**
     * @return array
     * @throws Exception
     */
    private function getDeckCodesTestData(): array {
        return array_merge(
            require(__DIR__.'/DeckCodesTestData.php'),
            require(__DIR__.'/RiotGamesTestData.php')
        );
    }

    /**
     * @throws Exception
     */
    public function testDeckEncodingSelfTestAndTestDataFailTest(): void
    {
        $previousExpectedDeck = null;
        foreach ($this->getDeckCodesTestData() as $deckCode => $expectedDeck) {

            if (!is_null($previousExpectedDeck)) {
                $encodedDeck = DeckEncoding::decode($deckCode);

                $this->assertNotEqualsCanonicalizing(
                    $previousExpectedDeck,
                    $encodedDeck
                );
            }

            $previousExpectedDeck = $expectedDeck;
        }
    }

    /**
     * @throws Exception
     */
    public function testDeckEncodingSelfTestAndTestDataTest(): void
    {
        foreach ($this->getDeckCodesTestData() as $deckCode => $expectedDeck) {
            $encodedDeck = DeckEncoding::decode($deckCode);

            $this->assertEqualsCanonicalizing(
                $expectedDeck,
                $encodedDeck,
                'Failed to verify Deck-Equality for DeckCode:'.$deckCode
            );

            $this->assertEquals(
                Base32::encode(substr(Base32::decode($deckCode), 1)),
                Base32::encode(substr(Base32::decode(DeckEncoding::encode($encodedDeck)), 1)),
                'Failed to verify DeckCode-Equality for DeckCode:'.$deckCode
            );
        }
    }

}