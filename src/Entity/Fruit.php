<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity()
 */
class Fruit implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $family;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $fruitOrder;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $genus;

    /**
     * @ORM\Column(type="json")
     */
    private array $nutritions;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Gets the name of the fruit.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of the fruit.
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the family of the fruit.
     *
     * @return string
     */
    public function getFamily(): string
    {
        return $this->family;
    }

    /**
     * Sets the family of the fruit.
     *
     * @param string $family
     * @return self
     */
    public function setFamily(string $family): self
    {
        $this->family = $family;

        return $this;
    }

    /**
     * @return string
     */
    public function getFruitOrder(): string
    {
        return $this->fruitOrder;
    }

    /**
     * @param string $fruitOrder
     */
    public function setFruitOrder(string $fruitOrder): void
    {
        $this->fruitOrder = $fruitOrder;
    }


    /**
     * Gets the genus of the fruit.
     *
     * @return string
     */
    public function getGenus(): string
    {
        return $this->genus;
    }

    /**
     * Sets the genus of the fruit.
     *
     * @param string $genus
     * @return self
     */
    public function setGenus(string $genus): self
    {
        $this->genus = $genus;

        return $this;
    }

    /**
     * Gets the nutritional values of the fruit.
     *
     * @return array
     */
    public function getNutritions(): array
    {
        return $this->nutritions;
    }

    /**
     * Sets the nutritional values of the fruit.
     *
     * @param array $nutritions
     * @return self
     */
    public function setNutritions(array $nutritions): self
    {
        $this->nutritions = $nutritions;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'family' => $this->family,
            'order' => $this->fruitOrder,
            'genus' => $this->genus,
            'nutritions' => $this->nutritions,
        ];
    }
}