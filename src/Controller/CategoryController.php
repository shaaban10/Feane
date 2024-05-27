<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('admin',name:'admin.')]

#[IsGranted('ROLE_ADMIN')]

class CategoryController extends AbstractController
{

    #[Route('/', name: 'admin.index')]
    public function admin() : Response{
        return $this->render('admin/admin.html.twig');
    }
    #[Route('/category', name: 'category.index')]
    public function index(CategoryRepository $repository): Response
    {
        $category = $repository->findAll();
        return $this->render('admin/category/index.html.twig',['category'=>$category]);
    }


    #[Route('/category/create', name: 'category.create')]
public function create( EntityManagerInterface $entity,Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->persist($category);
            $entity->flush();
            $this->addFlash('success', 'Category created successfully!');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/create.html.twig',['form'=>$form->createView()]);
    }

    #[Route('/category/{id}/edit', name: 'category.edit')]
public function edit(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully!');
            return $this->redirectToRoute('admin.category.index');

        }
        return $this->render('admin/category/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/category/{id}/show', name: 'category.show')]
    public function show(Category $category): Response{
        return $this->render('admin/category/show.html.twig',['category'=>$category]);
    }

    #[Route('/category/{id}/delete', name: 'category.delete')]
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success', 'Category deleted successfully!');
        return $this->redirectToRoute('admin.category.index');

    }

}
