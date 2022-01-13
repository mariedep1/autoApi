<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *@Route("/api")
 */
class AuthController extends AbstractController
{

    /**
     * @Route("/user/show", name="showuser", methods={"GET"})
     */
        public function show(UserInterface $currentUser, User $user=null){

            if(!$user){
                $user=$currentUser;
            }
                return $this->json($user, 200, [], ['groups'=>'user:details']);
        }

    /**
     * @Route("/user/edit/{id}", name="user_edit" , methods={"PATCH"})
     */
    public function edit(User $user, EntityManagerInterface $manager, Request $requete){

        $data = $requete->toArray();
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setSiret($data['siret']);
        $user->setEmail($data['email']);
        $user->setPhoneNumber($data['phone']);
        if($data['username']){
            $user->setUsername($data['username']);
        }
        $manager->persist($user);
        $manager->flush();

        $message = "utilisateur modifié";

        return $this->json($message, 200);
    }


}
