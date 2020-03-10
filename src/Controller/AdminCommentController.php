<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="admin_ads_comment")
     */
    public function index(CommentRepository $repo){
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repo->findAll(),
        ]);
    }

    /**
     * Permet de modifier un commentaire
     * 
     * @Route("admin/comment/{id}/edit", name="admin_comment_edit")
     *
     * @return Response
     */
    public function edit(Comment $comment, Request $req, EntityManagerInterface $manager){
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce n° <strong>{$comment->getId()}</strong> à bien été enregistrée !"
            );
        }


        return $this->render('admin/comment/edit.html.twig',[
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     *
     * @Route("admin/comment/{id}/delete" , name="admin_comment_delete")
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $manager){

        $this->addFlash(
            'success',
            "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> pour l'annonce <strong> {$comment->getAd()->getTitle()} </strong> à bien été supprimée !"
        );

        $manager->remove($comment);
        $manager->flush();
    
        return $this->redirectToRoute('admin_ads_comment');
    }
}
