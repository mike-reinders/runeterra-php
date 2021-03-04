<?php

namespace MikeReinders\RuneTerraPHP\Tests;

use MikeReinders\RuneTerraPHP\DeckEncodingFactory;
use PHPUnit\Framework\TestCase;

final class DeckEncodingTest extends TestCase
{

    public function testDeckEncodingSelfTestAndTestDataFailTest(): void
    {
        $previousExpectedDeck = null;
        foreach (TestUtils::getDeckCodeTestData() as $deckCode => $expectedDeck) {

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
        foreach (TestUtils::getDeckCodeTestData() as $deck_code => $expected_deck) {
            $encodedDeck = DeckEncodingFactory::toCardCodeDeck($deck_code);

            $this->assertEquals(
                $expected_deck,
                $encodedDeck
            );

            $this->assertEquals(
                TestUtils::removeVersionFromDeckCode($deck_code),
                TestUtils::removeVersionFromDeckCode(DeckEncodingFactory::fromCardCodeDeck($encodedDeck))
            );
        }
    }

}