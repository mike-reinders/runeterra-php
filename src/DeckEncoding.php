<?php

namespace MikeReinders\RuneTerraPHP;

use Base32\Base32;
use MikeReinders\RuneTerraPHP\Exception\EncodingException;
use MikeReinders\RuneTerraPHP\Exception\VarIntException;

/**
 * Class DeckEncoding
 * @package MikeReinders\RuneTerraPHP
 */
final class DeckEncoding {

    public const MAX_KNOWN_VERSION = 2;

    public const KNOWN_FACTIONS = [
        0 => [ 'DE', 'Demacia' ],
        1 => [ 'FR', 'Freljord' ],
        2 => [ 'IO', 'Ionia' ],
        3 => [ 'NX', 'Noxus' ],
        4 => [ 'PZ', 'Piltover & Zaun' ],
        5 => [ 'SI', 'Shadow Isles' ],
        6 => [ 'BW', 'Bilgewater' ],
        7 => [ 'SH', 'Shurima' ],
        9 => [ 'MT', 'Mount Targon' ]
    ];

    /**
     * @param string $deck_code
     * @return array
     */
    public static function decode(string $deck_code): array {
        try {
            $raw_deck = [];

            $bytes = Base32::decode($deck_code);
            $offset = 0;

            $firstByte = ord($bytes[0]);
            $offset++;

            // $format = $firstByte >> 4; @unused
            $version = $firstByte & 0xF;

            if ($version > DeckEncoding::MAX_KNOWN_VERSION) {
                throw new EncodingException('Unsupported deck code version '.$version.' > '.DeckEncoding::MAX_KNOWN_VERSION);
            }

            $bytesPopped = 0;
            for ($i = 3; $i > 0; $i--) {
                $numGroupOfs = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;

                for ($j = 0; $j < $numGroupOfs; $j++) {
                    $numOfsInThisGroup = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;
                    $set = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;
                    $faction_id = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;

                    for ($k = 0; $k < $numOfsInThisGroup; $k++) {
                        $card = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;

                        $raw_deck[] = [
                            $set,
                            $faction_id,
                            $card,
                            $i
                        ];
                    }
                }
            }

            while ((strlen($bytes) - $offset) > 0) {
                $count = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;
                $set = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;
                $faction_id = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;
                $number = VarInt::pop($bytes, $offset, $bytesPopped); $offset += $bytesPopped;

                $raw_deck[] = [
                    $set,
                    $faction_id,
                    $number,
                    $count
                ];
            }

            foreach ($raw_deck as $raw_card) {
                if (!self::isValidCard($raw_card)) {
                    throw new EncodingException('Invalid deck: card contains invalid values');
                }
            }

            return $raw_deck;
        } catch (VarIntException $ex) {
            throw new EncodingException('Invalid deck: VarInt failed to read integer', 0, $ex);
        }
    }


    /**
     * @param array $raw_deck
     * @return string
     */
    public static function encode(array $raw_deck): string {
        try {
            foreach ($raw_deck as $raw_card) {
                if (!self::isValidCard($raw_card)) {
                    throw new EncodingException('Invalid deck: card contains invalid values');
                }
            }

            return rtrim(Base32::encode(
                "\x12"
                .self::encodeGroup(self::groupByFactionAndSetSorted(self::getNcards($raw_deck, 3)))
                .self::encodeGroup(self::groupByFactionAndSetSorted(self::getNcards($raw_deck, 2)))
                .self::encodeGroup(self::groupByFactionAndSetSorted(self::getNcards($raw_deck, 1)))
                .self::encodeNofs($raw_deck)
            ), "=");
        } catch (VarIntException $ex) {
            throw new EncodingException('Invalid deck: VarInt failed to read integer', 0, $ex);
        }
    }

    private static function isValidCard(array $raw_card): bool {
        if (sizeof($raw_card) != 4) {
            return false;
        }

        foreach ($raw_card as $value) {
            if (!is_int($value)) {
                return false;
            }
        }

        if ($raw_card[0] < 0 || $raw_card[1] > 99) {
            return false;
        }

        if ($raw_card[1] < 0 || !isset(self::KNOWN_FACTIONS[$raw_card[1]])) {
            return false;
        }

        if ($raw_card[2] < 0 || $raw_card[2] > 999) {
            return false;
        }

        if ($raw_card[3] < 0) {
            return false;
        }

        return true;
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

}