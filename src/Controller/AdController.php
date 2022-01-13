<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Garage;
use App\Entity\Photo;
use App\Repository\AdRepository;
use App\Repository\FuelRepository;
use App\Repository\GarageRepository;
use App\Repository\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class AdController extends AbstractController
{
    /**
     * @Route("/ad", name="ad", methods={"GET"})
     */
    public function index(AdRepository $adRepository , Request $request): Response
    {
        $ads = $adRepository->findAll();

        return $this->json($ads, 200,[], ["groups"=>"ads:user"]);

    }

    /**
     * @Route("/ad/user/", name="ads_user", methods={"GET"})
     */
    public function  getAllByGarage(): Response{

        $user = $this->getUser();
        $ads = $user->getAds();
        return $this->json($ads, 200, [], ["groups"=>"ads:user"]);

    }
    /**
     * @Route("/ad/search", name="ad_search", methods={"POST"})
     */
    public function findBySearchBar(AdRepository $adRepository, Request $request): Response{
        $data = $request->toArray();
        $ads = $adRepository->findBySearch($data['year'],$data['kilometers'], $data['price'],
            $data['model'], $data['fuel'], $data['brand']);
        return $this->json($ads, 200, [], ["groups"=>"adlist"]);
    }

    /**
     * @Route("/ad/reference", name="ad_reference", methods={"POST"})
     */
    public function findByReference(AdRepository $adRepository, Request $request): Response{

        $data = $request->toArray();
        $ad = $adRepository->find($data['id']);

        return $this->json($ad, 200, [], ["groups"=>"ads:user"]);

    }

    /**
     * @Route("/ad/show/{id}", name="show_ad", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Ad $ad) : Response
    {
        return $this->json($ad, 200, [], ["groups"=>"adlist"]);
    }

    /**
     * @Route("/ad/delete/{id}", name="delete_ad", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(EntityManagerInterface $manager, Ad $ad): Response
    {
        $manager->remove($ad);
        $manager->flush();

        $message = "annonce supprimée";
        return $this->json($message, 200);
    }

    /**
     * @Route("/ad/new", name="new_ad", methods={"POST"})
     */
    public function create(Request $requete, EntityManagerInterface $manager,
    FuelRepository $fuelRepo, GarageRepository $garageRepository, ModelRepository $modelRepo): Response{

        $ad = new Ad();

        $dataAngular = $requete->toArray();


        $ad->setTitle($dataAngular["title"]);
        $ad->setDescription($dataAngular["description"]);
        $ad->setYear(new \DateTime($dataAngular['year']));
        $ad->setKilometers($dataAngular['kilometers']);
        $ad->setPrice($dataAngular['price']);


        $fuel = $fuelRepo->find($dataAngular["fuel"]);
        $model = $modelRepo->find($dataAngular["model"]);
        $garage = $garageRepository->find($dataAngular['garage_id']);
        $ad->setFuel($fuel);
        $ad->setModel($model);
        $ad->setGarage($garage);
        $ad->setUser($this->getUser());

        $folderPath = $this->getParameter('images_ads_path');
        $url = $this->getParameter('images_ads_url');

        foreach($dataAngular['fileSource'] as $key => $value){

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

        $manager->persist($ad);

        $manager->flush();

        $message = "annonce bien créée";

        return $this->json($message, 201);

    }

    /**
     *
     * @Route("/ad/edit/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(Ad $ad, Request $requete, EntityManagerInterface $manager,
                         FuelRepository $fuelRepository, GarageRepository $garageRepository,
                         ModelRepository $modelRepository): Response
    {
        $data = $requete->toArray();

        if(!empty($data['title'])){
            $ad->setTitle($data['title']);
        }
        if(!empty($data['description'])){
            $ad->setDescription($data['description']);
        }
        if(!empty($data['year'])){
            $ad->setYear(new \DateTime($data['year']));
        }
        if(!empty($data['kilometers'])){
            $ad->setKilometers($data['kilometers']);
        }
        if(!empty($data['price'])){
            $ad->setPrice($data['price']);
        }

        if(!empty($data['fuel'])){
            $fuel = $fuelRepository->find($data["fuel"]);
            $ad->setFuel($fuel);
        }
        if(!empty($data['model'])){
            $model = $modelRepository->find($data["model"]);
            $ad->setModel($model);

        }
        if(!empty($data['garage_id'])){
            $garage = $garageRepository->find($data['garage_id']);
            $ad->setGarage($garage);
        }

        $manager->persist($ad);

        $manager->flush();

        $message = "annonce bien modifié";

        return $this->json($message, 200);

    }

}
