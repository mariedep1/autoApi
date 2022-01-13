<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class PhotoController extends AbstractController
{
    /**
     * @Route("/photo/new", name="photo_new", methods={"POST"})
     */
    public function create(Request $requete, AdRepository $adRepository,EntityManagerInterface $manager): Response
    {
        $data = $requete->toArray();

        $id = $data['id'];
        $ad = $adRepository->find($id);
        $folderPath = $this->getParameter('images_ads_path');
        $url = $this->getParameter('images_ads_url');

        foreach($data['fileSource'] as $key => $value){

            $image_parts =explode(";base64,", $value);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $filename = 'ad'.uniqid() . '.' .$image_type;
            $file = $folderPath . $filename;
            file_put_contents($file, $image_base64);
            $photo = new Photo();
            $photo->setPath($url . $filename);
            $photo->setAd($ad);
            $manager->persist($photo);
        }

        $manager->flush();
        $message = "photo ajouté";

        return $this->json($message, 201);
    }

    /**
     * @Route("/photo/delete/{id}", name="photo_delete", methods={"DELETE"})
     */
    public function delete(Photo $photo, EntityManagerInterface $manager)
    {
        $manager->remove($photo);
        $manager->flush();
        $message = "photo supprimée";
        return $this->json($message, 200);
    }
}
