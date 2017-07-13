<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="food")
 * @Hateoas\Relation("self", href="expr('/foods/' ~ object.getId())")
 * @Hateoas\Relation("manufacturer", embedded="expr(object.getManufacturer())")
 */
class Food extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="foods")
     * @Assert\NotBlank()
     * @Serializer\Exclude()
     */
    protected $manufacturer;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $servingSize;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    protected $calories;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    protected $carbs;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    protected $fat;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    protected $protein;

    /**
     * @ORM\OneToMany(targetEntity="Diary", mappedBy="food")
     * @Serializer\Exclude()
     */
    private $diaryEntries;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getManufacturer(): Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    public function getServingSize(): string
    {
        return $this->servingSize;
    }

    public function setServingSize(string $servingSize)
    {
        $this->servingSize = $servingSize;
    }

    public function getCalories(): int
    {
        return $this->calories;
    }

    public function setCalories(int $calories)
    {
        $this->calories = $calories;
    }

    public function getCarbs(): int
    {
        return $this->carbs;
    }

    public function setCarbs(int $carbs)
    {
        $this->carbs = $carbs;
    }

    public function getFat(): int
    {
        return $this->fat;
    }

    public function setFat(int $fat)
    {
        $this->fat = $fat;
    }

    public function getProtein(): int
    {
        return $this->protein;
    }

    public function setProtein(int $protein)
    {
        $this->protein = $protein;
    }
}
