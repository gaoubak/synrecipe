<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends AbstractController
{
    #[Security("is_granted('ROLE_USER') and user === chosenUser")]
    #[Route('/utilisateur/edition/{id}', name: 'user.edit',  methods: ['GET', 'POST'] )]
    public function edit(User $chosenUser, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserType::class, $chosenUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($chosenUser, $form->getData()->getPlainPassword())) {
                $chosenUser = $form->getData();
                $manager->persist($chosenUser);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées.'
                );

                return $this->redirectToRoute('recipe_index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Security("is_granted('ROLE_USER') and user === chosenUser")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', name:'user.edit.password', methods: ['GET', 'POST'])]
    public function editpassword(User $chosenUser,Request $request, EntityManagerInterface $manager,UserPasswordHasherInterface $hasher): Response{
        $form = $this->createForm(UserPasswordType::class, $chosenUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($chosenUser, $form->getData()->getPlainPassword())) {
                $chosenUser->setPlainPassword(
                     $form->getData()->getnewPassword()
                );
                $chosenUser->setPassword(
                    $form->getData()->getPlainPassword()
               );
                $manager->persist($chosenUser);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Le mot de passse a ete modifier.'
                );

                return $this->redirectToRoute('recipe_index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }
        return $this->render('pages/user/edit_password.html.twig',[
            'form' => $form->createView(),
        ]);
    }
    #[Security("is_granted('ROLE_USER') and user === chosenUser")]
    #[Route('/utilisateur/delete/{id}', name:'recipe.delete', methods: ['GET'])]
    public function delete(User $chosenUser, Request $request, EntityManagerInterface $manager) : Response
    {
        
        if (!$chosenUser){
            return $this->redirectToRoute('recipe_index');
            $this->addFlash(
                'warning',
                'l\'utilisateur en question n\'a pas été trouver !'
            );

        }
        $manager->remove($chosenUser);
        $manager->flush();
        $this->addFlash(
            'success',
            'Vous avez été supprimer avec succès !'
        );

        return $this->redirectToRoute('security.login');
    }
}
