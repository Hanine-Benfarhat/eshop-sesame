<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository, LoggerInterface $logger): Response
    {
        $search = $request->query->get('search');
        $category = $request->query->get('category');
        $priceRange = $request->query->get('price_range');
        $sort = $request->query->get('sort');

        $logger->info('Product index accessed', [
            'search' => $search,
            'category' => $category,
            'price_range' => $priceRange,
            'sort' => $sort,
        ]);

        $products = $productRepository->findByFilters($search, $category, $priceRange, $sort);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'currentSort' => $sort,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SELLER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $product->setCreatedAt(new \DateTime());
        $product->setSeller($this->getUser());
        $product->setStatus('pending');

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('products_images_directory'),
                    $newFilename
                );

                $product->setImage($newFilename);
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Your product has been submitted and is pending approval from our team.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SELLER')]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Check if user is the owner or an admin
        if (!$this->isGranted('ROLE_ADMIN') && $product->getSeller() !== $this->getUser()) {
            $this->addFlash('error', 'You do not have permission to edit this product.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('products_images_directory'),
                    $newFilename
                );

                $product->setImage($newFilename);
            }

            // If a regular user edits their product, set status back to pending
            if (!$this->isGranted('ROLE_ADMIN') && $product->getStatus() === 'approved') {
                $product->setStatus('pending');
                $this->addFlash('info', 'Your product has been updated and is now pending re-approval.');
            } else {
                $this->addFlash('success', 'Product updated successfully!');
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SELLER')]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Check if user is the owner or an admin
        if (!$this->isGranted('ROLE_ADMIN') && $product->getSeller() !== $this->getUser()) {
            $this->addFlash('error', 'You do not have permission to delete this product.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            
            $this->addFlash('success', 'Product deleted successfully!');
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
