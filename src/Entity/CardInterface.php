<?php


namespace MikeReinders\RuneTerraPHP\Entity;


use MikeReinders\RuneTerraPHP\Exception\IncompleteCardException;

interface CardInterface
{

    /**
     * @return int|null
     */
    public function getSet(): ?int;

    /**
     * @param int|null $set
     * @return $this
     */
    public function setSet(?int $set): self;

    /**
     * @return int|null
     */
    public function getFactionId(): ?int;

    /**
     * @param int|null $faction_id
     * @return $this
     */
    public function setFactionId(?int $faction_id): self;

    /**
     * @return string|null
     */
    public function getFactionNameId(): ?string;

    /**
     * @return string|null
     */
    public function getFactionName(): ?string;

    /**
     * @return int|null
     */
    public function getNumber(): ?int;

    /**
     * @param int|null $number
     * @return $this
     */
    public function setNumber(?int $number): self;

    /**
     * @return int|null
     */
    public function getCount(): ?int;

    /**
     * @param int|null $count
     * @return $this
     */
    public function setCount(?int $count): self;

    /**
     * @return string
     * @throws IncompleteCardException
     */
    public function __toString(): string;

}