<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create sample users
        $admin = new User();
        $admin->setEmail('admin@sesame.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setIsActive(true);
        $manager->persist($admin);

        $user1 = new User();
        $user1->setEmail('user@sesame.com');
        $user1->setFirstName('John');
        $user1->setLastName('Doe');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'user123'));
        $user1->setIsActive(true);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('seller@sesame.com');
        $user2->setFirstName('Sarah');
        $user2->setLastName('Smith');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'seller123'));
        $user2->setIsActive(true);
        $manager->persist($user2);

        $manager->flush();

        // Create sample products with images and sellers
        $products = [
            [
                'name' => 'Laptop HP ProBook',
                'description' => 'Professional laptop with Intel i7 processor, 16GB RAM, 512GB SSD. Perfect for business and development work.',
                'price' => 899.99,
                'quantity' => 1,
                'category' => 'Electronics',
                'location' => 'Tunis',
                'condition' => 'used',
                'status' => 'approved',
                'seller' => $user1,
                'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500&h=500&fit=crop'
            ],
            [
                'name' => 'Wireless Mouse Logitech',
                'description' => 'Ergonomic wireless mouse with precision tracking and long battery life.',
                'price' => 29.99,
                'quantity' => 1,
                'category' => 'Electronics',
                'location' => 'Sfax',
                'condition' => 'new',
                'status' => 'approved',
                'seller' => $user2,
                'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500&h=500&fit=crop'
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'RGB backlit mechanical keyboard with Cherry MX switches.',
                'price' => 79.99,
                'quantity' => 1,
                'category' => 'Electronics',
                'location' => 'Sousse',
                'condition' => 'like_new',
                'status' => 'approved',
                'seller' => $user1,
                'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&h=500&fit=crop'
            ],
            [
                'name' => 'Monitor Dell 27"',
                'description' => '4K UHD monitor with IPS panel, 60Hz refresh rate.',
                'price' => 349.99,
                'quantity' => 1,
                'category' => 'Electronics',
                'location' => 'Tunis',
                'condition' => 'used',
                'status' => 'approved',
                'seller' => $user2,
                'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&h=500&fit=crop'
            ],
            [
                'name' => 'Office Chair Ergonomic',
                'description' => 'Comfortable ergonomic office chair with lumbar support.',
                'price' => 249.99,
                'quantity' => 1,
                'category' => 'Furniture',
                'location' => 'Ariana',
                'condition' => 'like_new',
                'status' => 'approved',
                'seller' => $user1,
                'image' => 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=500&h=500&fit=crop'
            ],
            [
                'name' => 'Noise Cancelling Headphones',
                'description' => 'Premium wireless headphones with active noise cancellation.',
                'price' => 199.99,
                'quantity' => 1,
                'category' => 'Electronics',
                'location' => 'Nabeul',
                'condition' => 'new',
                'status' => 'approved',
                'seller' => $user2,
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop'
            ],
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setDescription($productData['description']);
            $product->setPrice($productData['price']);
            $product->setQuantity($productData['quantity']);
            $product->setCategory($productData['category']);
            $product->setLocation($productData['location']);
            $product->setCondition($productData['condition']);
            $product->setStatus($productData['status']);
            $product->setSeller($productData['seller']);
            $product->setImage($productData['image']);
            $product->setCreatedAt(new \DateTime());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
