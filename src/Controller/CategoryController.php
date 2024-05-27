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

#[Route('admin',name:'admin.')]
class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category.index')]
    public function index(CategoryRepository $repository,EntityManagerInterface $entity): Response
    {
        $category = $repository->findAll();
        return $this->render('category/index.html.twig',['category'=>$category]);
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
        return $this->render('category/create.html.twig',['form'=>$form->createView()]);
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
        return $this->render('category/edit.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/category/{id}/show', name: 'category.show')]
    public function show(Category $category): Response{
        return $this->render('category/show.html.twig',['category'=>$category]);
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
