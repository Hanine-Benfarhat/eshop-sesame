<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        // Get featured or latest approved products for homepage
        $products = $productRepository->findApprovedProducts(6);
        
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/dashboard', name: 'app_user_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function userDashboard(ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        
        // Get all products by this user
        $allProducts = $productRepository->findBy(['seller' => $user], ['createdAt' => 'DESC']);
        
        // Organize by status
        $pendingProducts = array_filter($allProducts, fn($p) => $p->getStatus() === 'pending');
        $approvedProducts = array_filter($allProducts, fn($p) => $p->getStatus() === 'approved');
        $rejectedProducts = array_filter($allProducts, fn($p) => $p->getStatus() === 'rejected');
        
        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'allProducts' => $allProducts,
            'pendingProducts' => $pendingProducts,
            'approvedProducts' => $approvedProducts,
            'rejectedProducts' => $rejectedProducts,
            'stats' => [
                'total' => count($allProducts),
                'pending' => count($pendingProducts),
                'approved' => count($approvedProducts),
                'rejected' => count($rejectedProducts),
            ]
        ]);
    }
}
