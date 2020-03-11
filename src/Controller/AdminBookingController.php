<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/booking/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $repo, $page, PaginationService $pagination){

        $pagination->setEntityClass(Booking::class)
                   ->setPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de modifier une réservation
     * 
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit")
     *
     * @param Booking $booking
     * @param Request $req
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Booking $booking, Request $req, EntityManagerInterface $manager){

        $form = $this->createForm(AdminBookingType::class, $booking,[
            'validation_groups' => ["Default"]
        ]);

        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $booking->setAmount(0);
        
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n° <strong>{$booking->getId()}</strong> à bien été enregistrée !"
            );

            return $this->redirectToRoute('admin_booking_index');
        }

        return $this->render('admin/booking/edit.html.twig',[
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une réservation
     * 
     * @Route("/admin/booking/{id}/delete" ,name = "admin_booking_delete")
     *
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager){
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation à bien été supprimée"
        );

        return $this->redirectToRoute("admin_booking_index");
    }
}
