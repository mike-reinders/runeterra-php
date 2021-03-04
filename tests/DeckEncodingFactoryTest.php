<?php


namespace MikeReinders\RuneTerraPHP\Tests;


use MikeReinders\RuneTerraPHP\DeckEncodingFactory;
use PHPUnit\Framework\TestCase;

final class DeckEncodingFactoryTest extends TestCase
{

    public function testDeckEncodingChainingTestDataTest(): void
    {
        foreach (TestUtils::getDeckCodeTestData() as $deck_code => $expected_deck) {
            $raw_deck = DeckEncodingFactory::toRawDeck($deck_code);
            $raw_deck__deck_code = DeckEncodingFactory::fromRawDeck($raw_deck);
            $this->assertEquals(
                TestUtils::removeVersionFromDeckCode($deck_code),
                TestUtils::removeVersionFromDeckCode($raw_deck__deck_code)
            );

            $card_code_deck = DeckEncodingFactory::toCardCodeDeck($deck_code);
            $card_code_deck__deck_code = DeckEncodingFactory::fromCardCodeDeck($card_code_deck);
            $this->assertEquals(
                TestUtils::removeVersionFromDeckCode($deck_code),
                TestUtils::removeVersionFromDeckCode($card_code_deck__deck_code)
            );

            $card_deck = DeckEncodingFactory::toCardDeck($deck_code);
            $card_deck__deck_code = DeckEncodingFactory::fromCardDeck($card_deck);
            $this->assertEquals(
                TestUtils::removeVersionFromDeckCode($deck_code),
                TestUtils::removeVersionFromDeckCode($card_deck__deck_code)
            );
        }
    }

}