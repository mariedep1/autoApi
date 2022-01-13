<?php

namespace App\Controller;

use App\Entity\Garage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class GarageController extends AbstractController
{
    /**
     * @Route("/garage", name="garage", methods={"GET"})
     */
    public function index(){
        $user = $this->getUser();
        $garages = $user->getGarages();

        return $this->json($garages, 200, [], ["groups"=>"garage:liste"]);

    }
    /**
     * @Route("/garage/new", name="garage_new", methods={"POST"})
     * @Route("/garage/edit/{id}", name="garage_edit", methods={"PATCH"})
     */
    public function create(Garage $garage = null, Request $requete, EntityManagerInterface $manager, UserRepository $userRepository): Response
    {
        $modeEdition = true;

        if(!$garage){
            $garage = new Garage();
            $modeEdition = false;
        }

        $data = $requete->toArray();
        $garage->setName($data['name']);
        $garage->setAddress($data['address']);
        $garage->setPostcode($data['postcode']);
        $garage->setCity($data['city']);
        $garage->setPhoneNumber($data['phoneNumber']);

        if(!$modeEdition){
            $user = $this->getUser();
            $garage->setUser($user);
        }

        $manager->persist($garage);
        $manager->flush();

        $message="garage créé";
        return $this->json($message, 201);
    }

    /**
     * @Route("/garage/delete/{id}", name="delete_garage", methods={"DELETE"})
     */
    public function delete(Garage $garage, EntityManagerInterface $manager){
        $manager->remove($garage);
        $manager->flush();
        $message = "garage bien supprimé";
        return $this->json($message, 200);
    }

}
