<?php

namespace App\Controller;

use Bezhanov\Silex\Routing\Route;

class FoodController
{
    /**
     * @Route("/foods", methods={"GET"})
     */
    public function indexAction()
    {
        return 'collection of foods';
    }

    /**
     * @Route("/foods", methods={"POST"})
     */
    public function createAction()
    {
        return 'store new food';
    }

    /**
     * @Route("/foods/{id}", methods={"GET"})
     */
    public function readAction($id)
    {
        return "read $id";
    }

    /**
     * @Route("/foods/{id}", methods={"PUT", "PATCH"})
     */
    public function updateAction($id)
    {
        return "update $id";
    }

    /**
     * @Route("/foods/{id}", methods={"DELETE"})
     */
    public function deleteAction($id)
    {
        return "delete $id";
    }
}
