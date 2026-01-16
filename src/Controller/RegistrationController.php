<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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
            ->add('email', EmailType::class, ['attr' => ['class' => 'form-control']])
            ->add('firstName', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('lastName', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('password', PasswordType::class, ['mapped' => false, 'attr' => ['class' => 'form-control']])
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
