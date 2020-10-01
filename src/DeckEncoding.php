<?php

namespace MikeReinders\RuneTerraPHP;

use Base32\Base32;
use InvalidArgumentException;

/**
 * Class DeckEncoding
 * @package MikeReinders\RuneTerraPHP
 */
final class DeckEncoding {

    private const MAX_KNOWN_VERSION = 2;

    private const FACTIONS = [
        0 => 'DE',
        1 => 'FR',
        2 => 'IO',
        3 => 'NX',
        4 => 'PZ',
        5 => 'SI',
        6 => 'BW',
        9 => 'MT'
    ];

    /**
     * Decodes a deck code
     * e.g. CEAAECABAQJRWHBIFU2DOOYIAEBAMCIMCINCILJZAICACBANE4VCYBABAILR2HRL
     *
     * to an array of counted card identifiers like:
     *
     * [
     *    [
     *       0: set,            // int: min. 0 (usually up to 99 max)
     *       1: factionId,      // int: min. 0 (usually up to 5 max)
     *       2: cardNumber,     // int: min. 0 (usually up to 999 max)
     *       3: cardCount       // int: min. 1 (usually up to 3 max)
     *    ]
     *    ...
     * ]
     *
     * or an array of counted card codes like:
     *
     * [
     *    [
     *       0: cardCode     // string: e.g. 01DE123
     *       1: cardCount    // int: min. 1 (usually up to 3 max)
     *    ]
     *    ...
     * ]
     *
     * @param string $code
     * @param bool $cardCodes if true, outputs cardcodes instead of identifiers
     * @return array
     * @throws InvalidArgumentException when the given deck code couldn't be decoded to a deck/array of possible (valid) values
     */
    public static function decode(string $code, bool $cardCodes = true): array {
        $result = [];

        $bytes = Base32::decode($code);

        $firstByte = ord($bytes[0]); $bytes = substr($bytes, 1);
        $version = $firstByte & 0xF;

        if ($version > DeckEncoding::MAX_KNOWN_VERSION) {
            throw new InvalidArgumentException("Unsupported Version > ".DeckEncoding::MAX_KNOWN_VERSION);
        }

        for ($i = 3; $i > 0; $i--) {
            $numGroupOfs = VarInt::pop($bytes);

            for ($j = 0; $j < $numGroupOfs; $j++) {

                $numOfsInThisGroup = VarInt::pop($bytes);
                $set = VarInt::pop($bytes);
                $faction = VarInt::pop($bytes);

                for ($k = 0; $k < $numOfsInThisGroup; $k++) {
                    $card = VarInt::pop($bytes);

                    if ($cardCodes) {
                        $result[] = [
                            str_pad($set, 2, '0', STR_PAD_LEFT).self::factionById($faction).str_pad($card, 3, '0', STR_PAD_LEFT),
                            $i
                        ];
                    } else {
                        $result[] = [
                            $set,
                            $faction,
                            $card,
                            $i
                        ];
                    }
                }
            }
        }

        while (strlen($bytes) > 0) {
            $fourPlusCount = VarInt::pop($bytes);
            $fourPlusSet = VarInt::pop($bytes);
            $fourPlusFaction = VarInt::pop($bytes);
            $fourPlusNumber = VarInt::pop($bytes);

            if ($cardCodes) {
                $result[] = [
                    str_pad($fourPlusSet, 2, '0', STR_PAD_LEFT).self::factionById($fourPlusFaction).str_pad($fourPlusNumber, 3, '0', STR_PAD_LEFT),
                    $fourPlusCount
                ];
            } else {
                $result[] = [
                    $fourPlusSet,
                    $fourPlusFaction,
                    $fourPlusNumber,
                    $fourPlusCount
                ];
            }
        }

        return $result;
    }


    /**
     * Encodes an array of card identifiers like DeckEncoding::decode returns back to a deck code
     *
     * @param array $deck
     * @return string
     * @throws InvalidArgumentException when the given deck is invalid
     */
    public static function encode(array $deck): string {
        if (!self::isValidDeck($deck)) {
            throw new InvalidArgumentException("Given deck is invalid");
        }

        foreach ($deck as $key => $card) {
            if (sizeof($card) == 2) {
                if (preg_match("/^(\\d{2,})([A-Z]{2})(\\d{3,})$/", $card[0], $matches) !== 1) {
                    throw new InvalidArgumentException('Invalid CardCode');
                }
                $deck[$key] = [
                    intval($matches[1]),
                    self::factionIdByCode($matches[2]),
                    intval($matches[3]),
                    $card[1]
                ];
            }
        }

        return rtrim(Base32::encode(
            "\x11"
            .self::encodeGroup(self::groupByFactionAndSetSorted(self::getNcards($deck, 3)))
            .self::encodeGroup(self::groupByFactionAndSetSorted(self::getNcards($deck, 2)))
            .self::encodeGroup(self::groupByFactionAndSetSorted(self::getNcards($deck, 1)))
            .self::encodeNofs($deck)
        ), "=");
    }


    /**
     * @param array $deck
     * @param int $count
     * @return array
     */
    private static function getNcards(array &$deck, int $count): array {
        $return = [];

        foreach ($deck as $key => $card) {
            if ($card[3] == $count) {
                $return[] = $card;
                unset($deck[$key]);
            }
        }

        return $return;
    }


    /**
     * @param array $groups
     * @return string
     */
    private static function encodeGroup(array $groups): string {
        $result = VarInt::get(sizeof($groups));

        foreach ($groups as $group) {
            $result .= VarInt::get(sizeof($group));

            $first = $group[0];
            $result .= VarInt::get($first[0]);
            $result .= VarInt::get($first[1]);

            foreach ($group as $card) {
                $result .= VarInt::get($card[2]);
            }
        }

        return $result;
    }

    /**
     * @param array $Nofs
     * @return string
     */
    private static function encodeNofs(array $Nofs): string {
        self::sortCards($Nofs);

        $result = "";
        foreach ($Nofs as $card) {
            $result .= VarInt::get($card[3]);
            $result .= VarInt::get($card[0]);
            $result .= VarInt::get($card[1]);
            $result .= VarInt::get($card[2]);
        }

        return $result;
    }

    /**
     * @param array $cards
     * @return array
     */
    private static function groupByFactionAndSetSorted(array $cards): array {
        $result = [];

        while (sizeof($cards) > 0) {
            $set = [];

            $first = array_shift($cards);
            $set[] = $first;

            for ($i = sizeof($cards)-1; $i >= 0; $i--) {
                $compare = $cards[$i];

                if ($first[0] == $compare[0] && $first[1] == $compare[1]) {
                    $set[] = $compare;
                    array_splice($cards, $i, 1);
                }
            }

            self::sortCards($set);

            $result[] = $set;
        }

        usort($result, function ($a, $b): int {
            return sizeof($a) - sizeof($b);
        });

        return $result;
    }

    /**
     * @param array $cards
     */
    private static function sortCards(array &$cards): void {
        usort($cards, function($a, $b):int {
            if ($a[0] == $b[0]) {
                if ($a[1] == $b[1]) {
                    if ($a[2] == $b[2]) {
                        return 0;
                    } else {
                        return $a[2] > $b[2] ? 1 : -1;
                    }
                } else {
                    return $a[1] > $b[1] ? 1 : -1;
                }
            } else {
                return $a[0] > $b[0] ? 1 : -1;
            }
        });
    }


    /**
     * Determines whether the given deck is valid
     *
     * The deck must follow this pattern: (total cards must count exactly 40)
     *
     * [
     *    [
     *       0: set,            // int: min. 0 (usually up to 99 max)
     *       1: factionId,      // int: min. 0 (usually up to 5 max)
     *       2: cardNumber,     // int: min. 0 (usually up to 999 max)
     *       3: cardCount       // int: min. 1 (usually up to 3 max)
     *    ]
     *    ...
     * ]
     *
     * or this to pass the test:
     *
     * [
     *    [
     *       0: cardCode     // string: e.g. 01DE123
     *       1: cardCount    // int: min. 1 (usually up to 3 max)
     *    ]
     *    ...
     * ]
     *
     * @param array $deck
     * @return bool true if the deck is valid, false otherwise.
     */
    public static function isValidDeck(array $deck): bool {
        $totalCards = 0;
        foreach ($deck as $card) {
            if (!is_array($card)) {
                return false;
            }

            if (sizeof($card) == 2) {
                if (!is_string($card[0]??null) || preg_match("/^\d{2,}[A-Z]{2}\d{3,}$/", $card[0]) !== 1) {
                    return false;
                }

                if (!is_int($card[1]??null) || $card[1] < 1) {
                    return false;
                }
                $totalCards+=$card[1];
            } else if (sizeof($card) == 4) {
                if (!is_int($card[0]??null) || $card[0] < 0) {
                    return false;
                }

                if (!is_int($card[1]??null) || $card[1] < 0) {
                    return false;
                }

                if (!is_int($card[2]??null) || $card[2] < 0) {
                    return false;
                }

                if (!is_int($card[3]??null) || $card[3] < 1) {
                    return false;
                }
                $totalCards+=$card[3];
            } else {
                return false;
            }

        }

        if ($totalCards != 40) {
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     * @return string
     */
    private static function factionById(int $id): string {
        if (isset(self::FACTIONS[$id])) {
            return self::FACTIONS[$id];
        }

        throw new InvalidArgumentException('Invalid faction id');
    }

    /**
     * @param string $code
     * @return int
     */
    private static function factionIdByCode(string $code): int {
        if (($id = array_search($code, self::FACTIONS)) !== false) {
            return $id;
        }

        throw new InvalidArgumentException('Invalid faction code');
    }

}
