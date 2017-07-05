<?php

namespace App\Entity;

abstract class Entity
{
    public function fromArray(array $userInput, array $allowedFields = [])
    {
        foreach ($userInput as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $this->$key = $value;
            }
        }
    }
}
