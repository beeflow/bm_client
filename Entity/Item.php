<?php

declare(strict_types=1);

namespace BMClientBundle\Client\Entity;

class Item
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setId(int $id): Item
    {
        $this->id = $id;

        return $this;
    }

    public function setName(string $name): Item
    {
        $this->name = $name;

        return $this;
    }

    public function setAmount(int $amount): Item
    {
        $this->amount = $amount;

        return $this;
    }
}
