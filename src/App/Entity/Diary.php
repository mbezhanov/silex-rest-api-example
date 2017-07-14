<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DiaryRepository")
 * @ORM\Table(name="diary")
 */
class Diary extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Meal", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meal;

    /**
     * @ORM\ManyToOne(targetEntity="Food", inversedBy="diaryEntries", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $food;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMeal(): Meal
    {
        return $this->meal;
    }

    public function setMeal(Meal $meal): self
    {
        $this->meal = $meal;

        return $this;
    }

    public function getFood(): Food
    {
        return $this->food;
    }

    public function setFood(Food $food): self
    {
        $this->food = $food;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
