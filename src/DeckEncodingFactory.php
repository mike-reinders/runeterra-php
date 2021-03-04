<?php

namespace MikeReinders\RuneTerraPHP;

use InvalidArgumentException;
use MikeReinders\RuneTerraPHP\Entity\Card;
use MikeReinders\RuneTerraPHP\Entity\CardInterface;
use MikeReinders\RuneTerraPHP\Exception\EncodingException;
use ReflectionClass;
use ReflectionException;

/**
 * Class DeckEncodingFactory
 * @package MikeReinders\RuneTerraPHP
 */
final class DeckEncodingFactory {

    /**
     * @param string $deck_code
     * @return array
     */
    public static function toRawDeck(string $deck_code): array {
        return DeckEncoding::decode($deck_code);
    }

    /**
     * @param array $raw_deck
     * @return string
     */
    public static function fromRawDeck(array $raw_deck): string {
        return DeckEncoding::encode($raw_deck);
    }

    /**
     * @param string $deck_code
     * @return array
     */
    public static function toCardCodeDeck(string $deck_code): array {
        $raw_deck = DeckEncoding::decode($deck_code);
        $card_code_deck = [];

        foreach ($raw_deck as $raw_card) {
            if (($faction = DeckEncoding::KNOWN_FACTIONS[$raw_card[1]] ??null) === null) {
                throw new EncodingException('Invalid deck: faction not found');
            }

            $card_code = str_pad($raw_card[0], 2, '0', STR_PAD_LEFT) .$faction[0] .str_pad($raw_card[2], 3, '0', STR_PAD_LEFT);

            $card_code_deck[$card_code] = $raw_card[3];
        }

        return $card_code_deck;
    }

    /**
     * @param array $card_code_deck
     * @return string
     */
    public static function fromCardCodeDeck(array $card_code_deck): string {
        $raw_deck = [];

        foreach ($card_code_deck as $card_code => $count) {
            if (preg_match("/^(\d+)([a-zA-Z]+)(\d+)$/", $card_code, $matches) === 1) {
                $set = intval($matches[1]);
                $number = intval($matches[3]);

                foreach (DeckEncoding::KNOWN_FACTIONS as $index => $value) {
                    if ($value[0] === $matches[2]) {
                        $faction_id = $index;
                    }
                }
            }

            if (is_int($card_code_deck[$card_code])) {
                $count = $card_code_deck[$card_code];
            }

            $raw_deck[] = [
                $set ??null,
                $faction_id ??null,
                $number ??null,
                $count ??null
            ];
        }

        return DeckEncoding::encode($raw_deck);
    }

    /**
     * @param string $deck_code
     * @param string $class
     * @return array
     */
    public static function toCardDeck(string $deck_code, string $class = Card::class): array {
        self::validateClass($class);

        $raw_deck = DeckEncoding::decode($deck_code);
        $card_deck = [];

        foreach ($raw_deck as $raw_card) {
            /** @var $card CardInterface */
            $card = new $class();

            $card->setSet($raw_card[0]);
            $card->setFactionId($raw_card[1]);
            $card->setNumber($raw_card[2]);
            $card->setCount($raw_card[3]);

            $card_deck[] = $card;
        }

        return $card_deck;
    }

    /**
     * @param CardInterface[] $card_deck
     * @return string
     */
    public static function fromCardDeck(array $card_deck): string {
        $raw_deck = [];

        foreach ($card_deck as $card) {
            if (is_object($card) && $card instanceof CardInterface) {
                $raw_deck[] = [
                    $card->getSet(),
                    $card->getFactionId(),
                    $card->getNumber(),
                    $card->getCount()
                ];
            } else {
                $raw_deck[] = [
                    null,
                    null,
                    null,
                    null
                ];
            }
        }

        return DeckEncoding::encode($raw_deck);
    }

    /**
     * Validates the class existence and if it's instantiable
     *
     * @param string $class
     */
    private static function validateClass(string $class): void {
        if (!class_exists($class, false)) {
            throw new InvalidArgumentException('Class '.$class.' has not been found');
        }

        $reflect = null;
        $reflectMethod = null;
        try {
            $reflect = new ReflectionClass($class);

            $reflectMethod = $reflect->getMethod('__construct');
        } catch (ReflectionException $ignored) {}

        if ($reflect === null || !$reflect->implementsInterface(CardInterface::class)) {
            throw new InvalidArgumentException('Class '.$class.' has to implement '.CardInterface::class);
        }

        if ($reflectMethod === null || $reflectMethod->getNumberOfRequiredParameters() > 0) {
            throw new InvalidArgumentException('Class '.$class.' does not have a callable constructor');
        }
    }

}