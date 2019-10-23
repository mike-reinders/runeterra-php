<?php

namespace MikeReinders\RuneTerraPHP\Tests;

use Exception;
use MikeReinders\RuneTerraPHP\DeckEncoding;
use PHPUnit\Framework\TestCase;

final class DeckEncodingTest extends TestCase  {

    private $deckCodesTestData;

    /**
     * @return array
     * @throws Exception
     */
    private function getDeckCodesTestData() {
        if (is_null($this->deckCodesTestData)) {
            $cases = explode("\n\n", file_get_contents(__DIR__.'/DeckCodesTestData.txt'));

            $matches = [];
            foreach ($cases as $case) {
                if (preg_match("/^\\n*([A-Z0-9]+)((?:\\n\\d+\\:\\d{2}[A-Z]{2}\\d{3})*)\\n*$/", $case, $case_matches) !== 1) {
                    throw new Exception('Failed to validate deck codes test data');
                }
                $deckCode = $case_matches[1];
                $expectingDeck = explode("\n", trim($case_matches[2]));
                foreach ($expectingDeck as $key => $card) {
                    $cardInfo = explode(":", $card);

                    $expectingDeck[$key] = [$cardInfo[1], $cardInfo[0]];
                }

                $matches[$deckCode] = $expectingDeck;
            }

            $this->deckCodesTestData = $matches;
        }

        return $this->deckCodesTestData;
    }

    /**
     * @throws Exception
     */
    public function testDeckencodingSelftestAndTestdataFailTest(): void
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
    public function testDeckencodingSelftestAndTestdataTest(): void
    {
        foreach ($this->getDeckCodesTestData() as $deckCode => $expectedDeck) {
            $encodedDeck = DeckEncoding::decode($deckCode);

            $this->assertEqualsCanonicalizing(
                $expectedDeck,
                $encodedDeck,
                'Failed to verify Deck-Equality for DeckCode:'.$deckCode
            );

            $this->assertEquals(
                $deckCode,
                DeckEncoding::encode($encodedDeck),
                'Failed to verify DeckCode-Equality for DeckCode:'.$deckCode
            );
        }
    }

}