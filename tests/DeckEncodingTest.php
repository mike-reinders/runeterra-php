<?php

namespace MikeReinders\RuneTerraPHP\Tests;

use Base32\Base32;
use MikeReinders\RuneTerraPHP\DeckEncoding;
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

    public function testDeckEncodingValueTestDataTest(): void
    {
        foreach (TestUtils::getDeckCodeTestData() as $deck_code => $expected_deck) {
            $encoded_deck = DeckEncoding::decode($deck_code);
            $decoded_deck_code = DeckEncoding::encode($encoded_deck);

            $firstByte = ord(substr(Base32::decode($decoded_deck_code), 0, 1));
            $format = $firstByte >> 4;
            $version = $firstByte & 0xF;

            $this->assertEquals(
                DeckEncoding::CURRENT_FORMAT,
                $format
            );

            /*$this->assertEquals(
                DeckEncoding::CURRENT_VERSION,
                $version
            );*/

            $this->assertEquals(
                TestUtils::removeVersionFromDeckCode($deck_code),
                TestUtils::removeVersionFromDeckCode($decoded_deck_code)
            );
        }
    }

}