<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class ManufacturerController
{
    /**
     * @Route("/manufacturers", methods={"GET"})
     */
    public function indexAction()
    {
        return 'collection of manufacturers';
    }

    /**
     * @Route("/manufacturers", methods={"POST"})
     */
    public function createAction()
    {
        return 'store stuff';
    }

    /**
     * @Route("/manufacturers/{id}", methods={"GET"})
     */
    public function readAction($id)
    {
        return "read $id";
    }

    /**
     * @Route("/manufacturers/{id}", methods={"PUT", "PATCH"})
     */
    public function updateAction($id)
    {
        return "update $id";
    }

    /**
     * @Route("/manufacturers/{id}", methods={"DELETE"})
     */
    public function deleteAction($id)
    {
        return "delete $id";
    }
}
