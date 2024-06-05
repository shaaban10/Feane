<?php

namespace App\Controller;
use App\Entity\Orders;
use App\Entity\Addresses;
use App\Entity\User;
use App\Form\AdressesType;
use App\Repository\AddressesRepository;
use App\Repository\OrdersRepository;
use App\Security\Voter\AddresseVoter;

use Doctrine\ORM\EntityManagerInterface;
use Stripe\Climate\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/addresses', name: 'addresses_')]
class AddressesController extends AbstractController
{


    #[Route('/', name: 'index')]
    #[isGranted(AddresseVoter::LIST)]
    public function index(AddressesRepository $repository, Request $request,SessionInterface $session)
    {


        $page = $request->query->getInt('page', 1);
        $user = $this->getUser();
        $canListAll = $this->isGranted(AddresseVoter::LISTALL);

        $addresses = $repository->AdressesPaginator($page, $canListAll ? null : $user->getId());
        $reference = $request->request->get('refernce');
        return $this->render('addresses/index.html.twig', ['addresses' => $addresses,'reference'=>$reference]);
    }

    #[Route('/add', name: 'add')]
    #[isGranted(AddresseVoter::CREATE)]
    public function add(EntityManagerInterface $em,Request $request): Response
    {
      $addresses = new Addresses();
      $user = $this->getUser();
      $addresses->setUser($user);
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


    #[Route('/addresses/select', name: 'select')]

    public function selectAddress(Request $request, SessionInterface $session,EntityManagerInterface $em)
    {
        $selectedAddressId = $request->request->get('selectedAddress');
        if ($selectedAddressId ) {
            // Rediriger vers la page de paiement
            return $this->redirectToRoute('orders_add', [
                'id' => $selectedAddressId
            ]);
        }

        // Si aucune adresse n'est sélectionnée, vous pouvez gérer l'erreur ici
        return $this->redirectToRoute('addresses_index');
    }
}
