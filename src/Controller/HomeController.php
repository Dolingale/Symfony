<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController{

    /**
     * @Route("/", name = "homepage")
     */

    public function home(){

        $prenoms = ["Lior" => 31,"Joseph" => 12, "Anne" => 55];

        return $this->render(
            'home.html.twig',
            ['title' => "Eh sa fait BIM BAM BOUM!!!",
            'age' => 14,
            'prenoms' => $prenoms]
        );
    }

    /**
     * @Route("/hello/{prenom}/{age}", name = "hello")
     * @Route("/bonjour", name="hello_base")
     * Montre la page qui dit Bonjour
     */

    public function hello($prenom = "Anonymous", $age = 35){
        return $this->render(
            'hello.html.twig',[
                'prenom' => $prenom,
                'age' => $age
            ]
        );
    }
}