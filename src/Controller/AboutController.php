<?php

namespace App\Controller;

use App\Entity\Carouselle;
use App\Repository\CarouselleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function index(CarouselleRepository $carouselleRepository): Response
    {
        $carousselle =$carouselleRepository->findAll();
        return $this->render('about/index.html.twig', [
            'controller_name' => 'AboutController',
            'carouselle'=>$carousselle
        ]);
    }
}
