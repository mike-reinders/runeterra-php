<?php


namespace MikeReinders\RuneTerraPHP\Entity;


use MikeReinders\RuneTerraPHP\DeckEncoding;

class Card implements CardInterface
{

    private $set;
    private $faction_id;
    private $number;
    private $count;

    /**
     * {@inheritDoc}
     */
    public function getSet(): ?int {
        return $this->set;
    }

    /**
     * {@inheritDoc}
     */
    public function setSet(?int $set): CardInterface {
        $this->set = $set;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFactionId(): ?int {
        return $this->faction_id;
    }

    /**
     * {@inheritDoc}
     */
    public function setFactionId(?int $faction_id): CardInterface {
        $this->faction_id = $faction_id;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFactionNameId(): ?string {
        if (($id = $this->getFactionId()) !== null) {
            return (DeckEncoding::KNOWN_FACTIONS[$id] ??[])[0] ??null;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getFactionName(): ?string {
        if (($id = $this->getFactionId()) !== null) {
            return (DeckEncoding::KNOWN_FACTIONS[$id] ??[])[1] ??null;
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getNumber(): ?int {
        return $this->number;
    }

    /**
     * {@inheritDoc}
     */
    public function setNumber(?int $number): CardInterface {
        $this->number = $number;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCount(): ?int {
        return $this->count;
    }

    /**
     * {@inheritDoc}
     */
    public function setCount(?int $count): CardInterface {
        $this->count = $count;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string {
        $output = $this->set === null? '': str_pad($this->set, 2, '0', STR_PAD_LEFT);
        $output .= ($faction_name_id = $this->getFactionNameId()) === null? '': $faction_name_id;
        $output .= $this->number === null? '': str_pad($this->number, 3, '0', STR_PAD_LEFT);

        return $output;
    }

}