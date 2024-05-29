<?php

namespace App\Controller;

use App\Entity\Addresses;
use App\Form\AdressesType;
use App\Repository\AddressesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/addresses', name: 'addresses_')]
class AddressesController extends AbstractController
{
    #[Route('/',name:'index')]
    public function index(AddressesRepository $repository){
        $addresses = $repository->findAll();
        return $this->render('addresses/index.html.twig',['addresses'=>$addresses]);

    }

    #[Route('/add', name: 'add')]
    public function add(EntityManagerInterface $em,Request $request): Response
    {
      $addresses = new Addresses();
      $form = $this->createForm(AdressesType::class , $addresses);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()){

          $em->persist($addresses);
          $em->flush();
          $this->addFlash('message','Votre addresse abien été ajouté');
          return $this->redirectToRoute('addresses_index');
      }
        return $this->render('addresses/create.html.twig',['form'=>$form]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(EntityManagerInterface $em,Addresses $addresses,Request $request): Response
    {
        $form =$this->createForm(AdressesType::class ,$addresses);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $em->flush();
            $this->addFlash('message','Votre addresses a bien été modifer');
            return $this->redirectToRoute('addresses_index');
        }
        return $this->render('addresses/edit.html.twig',['form'=>$form]);

    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(int $id,EntityManagerInterface $em): Response
    {
        $addresses = $em->getRepository(Addresses::class)->find($id);
        $em->remove($addresses);
        $em->flush();
        $this->addFlash('warning','votre Adresses a été bien supprieme');
        return $this->redirectToRoute('addresses_index');
    }
}
