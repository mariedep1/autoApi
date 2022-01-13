<?php

namespace App\Controller;

use App\Repository\FuelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class FuelController extends AbstractController
{
    /**
     * @Route("/fuel", name="fuel", methods={"GET"})
     */
    public function index(FuelRepository $fuelRepo): Response
    {
        $brands = $fuelRepo->findAll();
        return $this->json($brands, 200,[], ["groups"=>"fuel:list"]);
    }
}
