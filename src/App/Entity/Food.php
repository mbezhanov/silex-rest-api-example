<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="food")
 */
class Food
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="foods")
     */
    private $manufacturer;

    /**
     * @ORM\Column(type="string")
     */
    private $servingSize;

    /**
     * @ORM\Column(type="integer")
     */
    private $calories;

    /**
     * @ORM\Column(type="integer")
     */
    private $carbs;

    /**
     * @ORM\Column(type="integer")
     */
    private $fat;

    /**
     * @ORM\Column(type="integer")
     */
    private $protein;

    /**
     * @ORM\Column(type="integer")
     */
    private $sugar;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @return mixed
     */
    public function getServingSize()
    {
        return $this->servingSize;
    }

    /**
     * @param mixed $servingSize
     */
    public function setServingSize($servingSize)
    {
        $this->servingSize = $servingSize;
    }

    /**
     * @return mixed
     */
    public function getCalories()
    {
        return $this->calories;
    }

    /**
     * @param mixed $calories
     */
    public function setCalories($calories)
    {
        $this->calories = $calories;
    }

    /**
     * @return mixed
     */
    public function getCarbs()
    {
        return $this->carbs;
    }

    /**
     * @param mixed $carbs
     */
    public function setCarbs($carbs)
    {
        $this->carbs = $carbs;
    }

    /**
     * @return mixed
     */
    public function getFat()
    {
        return $this->fat;
    }

    /**
     * @param mixed $fat
     */
    public function setFat($fat)
    {
        $this->fat = $fat;
    }

    /**
     * @return mixed
     */
    public function getProtein()
    {
        return $this->protein;
    }

    /**
     * @param mixed $protein
     */
    public function setProtein($protein)
    {
        $this->protein = $protein;
    }

    /**
     * @return mixed
     */
    public function getSugar()
    {
        return $this->sugar;
    }

    /**
     * @param mixed $sugar
     */
    public function setSugar($sugar)
    {
        $this->sugar = $sugar;
    }
}
