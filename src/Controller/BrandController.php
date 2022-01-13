<?php

namespace App\Controller;

use App\Repository\BrandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class BrandController extends AbstractController
{
    /**
     * @Route("/brand", name="brand", methods={"GET"})
     */
    public function index(BrandRepository $brandRepo): Response
    {
        $brands = $brandRepo->findAll();
        return $this->json($brands, 200,[], ["groups"=>"brand:list"]);
    }
}
