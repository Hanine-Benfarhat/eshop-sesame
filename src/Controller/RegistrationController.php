<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        // Redirect if already logged in
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'your.email@example.com'],
                'label' => 'Email Address',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter your email']),
                    new Assert\Email(['message' => 'Please enter a valid email address']),
                ]
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'John'],
                'label' => 'First Name',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter your first name']),
                ]
            ])
            ->add('lastName', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Doe'],
                'label' => 'Last Name',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter your last name']),
                ]
            ])
            ->add('phone', TelType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => '+216 XX XXX XXX'],
                'label' => 'Phone Number',
                'required' => false,
            ])
            ->add('accountType', ChoiceType::class, [
                'choices' => [
                    'Buyer - I want to buy products' => 'buyer',
                    'Seller - I want to sell products' => 'seller',
                    'Both - I want to buy and sell' => 'both',
                ],
                'mapped' => false,
                'attr' => ['class' => 'form-select'],
                'label' => 'Account Type',
                'expanded' => true,
                'data' => 'buyer',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please select an account type']),
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['class' => 'form-control', 'placeholder' => '••••••••'],
                    'label' => 'Password'
                ],
                'second_options' => [
                    'attr' => ['class' => 'form-control', 'placeholder' => '••••••••'],
                    'label' => 'Confirm Password'
                ],
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter a password']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Your password must be at least {{ limit }} characters long',
                    ]),
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if (!$plainPassword) {
                $this->addFlash('danger', 'Password is required.');
                return $this->redirectToRoute('app_register');
            }
            
            $hashed = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashed);
            $user->setIsActive(true);

            // Handle roles based on account type selection
            $accountType = $form->get('accountType')->getData();
            $roles = ['ROLE_USER'];
            
            if ($accountType === 'seller' || $accountType === 'both') {
                $roles[] = 'ROLE_SELLER';
            }
            
            $user->setRoles($roles);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Registration successful! You can now login.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
