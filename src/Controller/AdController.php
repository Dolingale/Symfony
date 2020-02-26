<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnounceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();


        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * Permet de créer une annonce
     * 
     * @Route("/ads/new", name="ads_create")
     * 
     * @return Response
     */

    public function create(Request $request, EntityManagerInterface $manager){
        $ad = new Ad();

        $image = new Image();

        $image->setUrl('http://placehold.it/400x200')
              ->setCaption('Titre 1');

        $image2 = new Image();

        $image2->setUrl('http://placehold.it/400x200')
              ->setCaption('Titre 2');

        $ad->addImage($image)
           ->addImage($image2);

        $form = $this->createForm(AnnounceType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> à bien été enregistrée !"
            );

            return $this->redirectToRoute('ads_show',[
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher une seule annonce
     * 
     * @Route("ads/{slug}", name="ads_show")
     *
     * @return Response
     */
    public function show($slug, Ad $ad){

        return $this->render('ad/show.html.twig',[
            'ad' => $ad
        ]);
    }

    
}
