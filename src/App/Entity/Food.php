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
}
