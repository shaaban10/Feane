<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\CarouselleRepository;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]

    public function index(Request $request,EntityManagerInterface $em,RecipeRepository $repository,CategoryRepository $categoryRepository,CarouselleRepository $carouselleRepository): Response
    {
        $recipes = $repository->findAll();
        $categories = $categoryRepository->findAll();
        $carouselle = $carouselleRepository->findAll();

        $reservations = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservations);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservations);
            $em->flush();
            $this->addFlash('succes','Votre Reservation a bien été pris en compte');
            return $this->redirectToRoute('app_home');

        }
        return $this->render('home/index.html.twig',['recipes'=>$recipes,'categories'=>$categories,'carouselle'=>$carouselle,'form'=>$form->createView()]);
    }




    }