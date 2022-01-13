<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AdRepository;
use App\Repository\GarageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *@Route("/api")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/admin/users")
     */
    public function getAllUsers(UserRepository $userRepository){
        $users = $userRepository->findAll();
        return $this->json($users, 200, [], ['groups'=>'user:details']);
    }

    /**
     *
     * @Route("/register", name="register", methods={"POST"})
     *
     */
    public function register(Request $requete, UserRepository $userRepo, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher)
    {

        $user = new User();
        $data = $requete->toArray();
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setUsername($data['username']);
        $user->setSiret($data['siret']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phone']);
        $password = $hasher->hashPassword($user, $data["password"]);
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $message = "utilisateur bien créé";

        return $this->json($message, 201);
    }

    /**
     * @Route("/admin/deleteUser/{id}", name="admin_deleteUser", methods={"DELETE"})
     */
    public function deleteUser(UserRepository $userRepo, $id, EntityManagerInterface $manager): Response
    {
       $user = $userRepo->find($id);
       $manager->remove($user);
       $manager->flush();
       $message = "utilisateur bien supprimé";
       return $this->json($message, 200);
    }

    /**
     * @Route("/admin/infos", name="", methods={"GET"})
     */
    public function getInfos(UserRepository $userRepo, GarageRepository $garageRepo, AdRepository $adRepo){
        $garages = count($garageRepo->findAll());
        $users = count($userRepo->findAll());
        $ads = count($adRepo->findAll());
        $infos = ['users'=>$users, 'garages'=>$garages, 'ads'=>$ads];
        return $this->json($infos, 200);
    }

    /**
     * @Route("/admin/garages", name="admin_garages", methods={"GET"})
     */
    public function getAllGarages(GarageRepository $garageRepository){

        $garages = $garageRepository->findAll();
        return $this->json($garages, 200, [], ["groups"=>"garage:liste"]);

    }

}
