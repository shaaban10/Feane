<?php

namespace App\Controller;

use App\Entity\Carouselle;
use App\Entity\Category;
use App\Form\CarouselleType;
use App\Form\CategoryType;
use App\Repository\CarouselleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('admin',name:'admin.')]
#[IsGranted('ROLE_ADMIN')]
class CarouselleController extends AbstractController
{
    #[Route('/carouselle', name: 'carouselle.index')]
    public function index(CarouselleRepository $carouselleRepository): Response
    {
        $carouselle = $carouselleRepository->findAll();


        return $this->render('admin/carouselle/index.html.twig',['carouselle'=>$carouselle]);
    }


    #[Route('/carouselle/create', name: 'carouselle.create')]
    public function create( Request $request,EntityManagerInterface $em ): Response
    {
        $carouselle = new Carouselle();
        $form = $this->createForm(CarouselleType::class,$carouselle);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($carouselle);
            $em->flush();
            $this->addFlash('succes','you added a new carouselle');
            return $this->redirectToRoute('admin.carouselle.index');

        }

        return $this->render('admin/carouselle/create.html.twig',['carouselle'=>$carouselle,'form'=>$form->createView()]);
    }

    #[Route('/carouselle/{id}/edit', name: 'carouselle.edit')]
    public function edit(Carouselle $carouselle,EntityManagerInterface $em,Request $request): Response
    {
            $form = $this->createForm(CarouselleType::class,$carouselle);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $em->persist($carouselle);
                $em->flush();
                $this->addFlash('succes','you updated a carouselle');
                return $this->redirectToRoute('admin.carouselle.index');
            }


        return $this->render('admin/carouselle/edit.html.twig',['form'=>$form->createView()]);
    }

    #[Route('/carouselle/{id}/show', name: 'carouselle.show')]
    public function show(Carouselle $carouselle): Response{


        return $this->render('admin/carouselle/show.html.twig',['carouselle'=>$carouselle]);
    }

    #[Route('/carouselle/{id}/delete', name: 'carouselle.delete')]
    public function delete(Carouselle $carouselle,EntityManagerInterface $em): Response
    {
        $em->remove($carouselle);
        $em->flush();
        $this->addFlash('succes','you deleted a carouselle');
        return $this->redirectToRoute('admin.carouselle.index');


    }

}
