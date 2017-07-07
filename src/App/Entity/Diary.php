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

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function getMeal(): Meal
    {
        return $this->meal;
    }

    public function setMeal(Meal $meal)
    {
        $this->meal = $meal;
    }


    public function getFood(): Food
    {
        return $this->food;
    }

    public function setFood(Food $food)
    {
        $this->food = $food;
    }
}
