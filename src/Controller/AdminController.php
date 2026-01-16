<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(ProductRepository $productRepository, UserRepository $userRepository): Response
    {
        $pendingProducts = $productRepository->findBy(['status' => 'pending'], ['createdAt' => 'DESC']);
        $approvedProducts = $productRepository->findBy(['status' => 'approved'], ['createdAt' => 'DESC'], 10);
        $rejectedProducts = $productRepository->findBy(['status' => 'rejected'], ['createdAt' => 'DESC'], 10);

        // Get statistics
        $totalUsers = count($userRepository->findAll());
        $totalProducts = count($productRepository->findAll());
        $totalApproved = count($productRepository->findBy(['status' => 'approved']));
        $totalRejected = count($productRepository->findBy(['status' => 'rejected']));

        return $this->render('admin/dashboard.html.twig', [
            'pendingProducts' => $pendingProducts,
            'approvedProducts' => $approvedProducts,
            'rejectedProducts' => $rejectedProducts,
            'pendingCount' => count($pendingProducts),
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
        ]);
    }

    #[Route('/users', name: 'admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/products', name: 'admin_products')]
    public function products(Request $request, ProductRepository $productRepository): Response
    {
        $filter = $request->query->get('filter', 'all');
        
        if ($filter === 'all') {
            $products = $productRepository->findBy([], ['createdAt' => 'DESC']);
        } else {
            $products = $productRepository->findBy(['status' => $filter], ['createdAt' => 'DESC']);
        }

        // Calculate statistics
        $stats = [
            'total' => count($productRepository->findAll()),
            'pending' => count($productRepository->findBy(['status' => 'pending'])),
            'approved' => count($productRepository->findBy(['status' => 'approved'])),
            'rejected' => count($productRepository->findBy(['status' => 'rejected'])),
        ];

        return $this->render('admin/products.html.twig', [
            'products' => $products,
            'filter' => $filter,
            'stats' => $stats,
        ]);
    }

    #[Route('/settings', name: 'admin_settings')]
    public function settings(): Response
    {
        return $this->render('admin/settings.html.twig');
    }

    #[Route('/product/{id}/approve', name: 'admin_product_approve', methods: ['POST'])]
    public function approveProduct(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('approve'.$product->getId(), $request->request->get('_token'))) {
            $product->setStatus('approved');
            $em->flush();

            $this->addFlash('success', 'Product "' . $product->getName() . '" has been approved.');
        } else {
            $this->addFlash('danger', 'Invalid CSRF token.');
        }
        
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/product/{id}/reject', name: 'admin_product_reject', methods: ['POST'])]
    public function rejectProduct(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('reject'.$product->getId(), $request->request->get('_token'))) {
            $reason = $request->request->get('reason', 'Does not meet our guidelines');
            $product->setStatus('rejected');
            $product->setRejectionReason($reason);
            $em->flush();

            $this->addFlash('warning', 'Product "' . $product->getName() . '" has been rejected.');
        } else {
            $this->addFlash('danger', 'Invalid CSRF token.');
        }
        
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/user/{id}/toggle-admin', name: 'admin_user_toggle_admin', methods: ['POST'])]
    public function toggleAdminRole(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('toggle-admin'.$user->getId(), $request->request->get('_token'))) {
            $roles = $user->getRoles();
            
            if (in_array('ROLE_ADMIN', $roles)) {
                // Remove admin role
                $roles = array_diff($roles, ['ROLE_ADMIN']);
                $user->setRoles(array_values($roles));
                $this->addFlash('success', 'Admin role removed from ' . $user->getEmail());
            } else {
                // Add admin role
                $roles[] = 'ROLE_ADMIN';
                $user->setRoles($roles);
                $this->addFlash('success', 'Admin role granted to ' . $user->getEmail());
            }
            
            $em->flush();
        } else {
            $this->addFlash('danger', 'Invalid CSRF token.');
        }
        
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/analytics', name: 'admin_analytics')]
    public function analytics(ProductRepository $productRepository, UserRepository $userRepository): Response
    {
        $now = new \DateTime();
        $thirtyDaysAgo = (new \DateTime())->modify('-30 days');
        $sevenDaysAgo = (new \DateTime())->modify('-7 days');

        // User analytics
        $totalUsers = count($userRepository->findAll());
        $usersThisMonth = count($userRepository->createQueryBuilder('u')
            ->where('u.createdAt >= :date')
            ->setParameter('date', $thirtyDaysAgo)
            ->getQuery()
            ->getResult());
        $usersThisWeek = count($userRepository->createQueryBuilder('u')
            ->where('u.createdAt >= :date')
            ->setParameter('date', $sevenDaysAgo)
            ->getQuery()
            ->getResult());

        // Product analytics
        $totalProducts = count($productRepository->findAll());
        $productsThisMonth = count($productRepository->createQueryBuilder('p')
            ->where('p.createdAt >= :date')
            ->setParameter('date', $thirtyDaysAgo)
            ->getQuery()
            ->getResult());
        $productsThisWeek = count($productRepository->createQueryBuilder('p')
            ->where('p.createdAt >= :date')
            ->setParameter('date', $sevenDaysAgo)
            ->getQuery()
            ->getResult());

        // Status breakdown
        $pendingCount = count($productRepository->findBy(['status' => 'pending']));
        $approvedCount = count($productRepository->findBy(['status' => 'approved']));
        $rejectedCount = count($productRepository->findBy(['status' => 'rejected']));

        // Category statistics
        $categoryStats = $productRepository->createQueryBuilder('p')
            ->select('p.category, COUNT(p.id) as count')
            ->groupBy('p.category')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();

        // Top sellers
        $topSellers = $userRepository->createQueryBuilder('u')
            ->select('u', 'COUNT(p.id) as productCount')
            ->leftJoin('u.products', 'p')
            ->groupBy('u.id')
            ->orderBy('productCount', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        // Recent activity
        $recentProducts = $productRepository->findBy([], ['createdAt' => 'DESC'], 10);
        $recentUsers = $userRepository->findBy([], ['createdAt' => 'DESC'], 10);

        return $this->render('admin/analytics.html.twig', [
            'totalUsers' => $totalUsers,
            'usersThisMonth' => $usersThisMonth,
            'usersThisWeek' => $usersThisWeek,
            'totalProducts' => $totalProducts,
            'productsThisMonth' => $productsThisMonth,
            'productsThisWeek' => $productsThisWeek,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'categoryStats' => $categoryStats,
            'topSellers' => $topSellers,
            'recentProducts' => $recentProducts,
            'recentUsers' => $recentUsers,
        ]);
    }
}
