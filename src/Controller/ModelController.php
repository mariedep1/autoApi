<?php

namespace App\Controller;

use App\Repository\ModelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 *@Route("/api")
 */
class ModelController extends AbstractController
{
    /**
     * @Route("/model/{parameter}", name="model", methods={"GET"})
     */
    public function index(ModelRepository $modelRepo, $parameter): Response
    {
        $models = $modelRepo->findBy(["brand"=>$parameter]);
        return $this->json($models, 200, [], ["groups"=>"model:list"]);
    }
}
