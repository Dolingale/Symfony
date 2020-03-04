<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
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

    public function create(Request $request, EntityManagerInterface $manager, AdRepository $repo){
        $new_ad = new Ad();

        $form = $this->createForm(AnnonceType::class, $new_ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            
            foreach($new_ad->getImages() as $image){
                $image->setAd($new_ad);
                $manager->persist($image);
            }

            $new_ad->setAuthor($this->getUser());

            $manager->persist($new_ad);

            $ad = $this->getDoctrine()->getRepository(Ad::class)->findOneBy(['slug' => $new_ad->getSlug()]); // Vérifie si le nouveau slug est déja dans la BDD

            if($ad != null){ // Vérifie si $ad est différent de null et s'il est différent de null le concatène avec "-" + un numéro
                $i = 1;
                while ($this->getDoctrine()->getRepository(Ad::class)->findOneBy(['slug' => $new_ad->getSlug()]) != null){
                    $i++;
                    $new_ad->setSlug($new_ad->getSlug(). "-". $i);
                }
            }

            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$new_ad->getTitle()}</strong> à bien été enregistrée !"
            );


            return $this->redirectToRoute('ads_show',[
                'slug' => $new_ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager){

        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
            );

            return $this->redirectToRoute('ads_show',[
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/edit.html.twig',[
            'form' => $form->createView(),
            'ad' => $ad
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
