<?php

namespace App\Controller;

use App\Entity\Keyboard;
use App\Form\KeyboardType;
use App\Repository\KeyboardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


#[Route('/keyboard')]
final class KeyboardController extends AbstractController
{
    #[Route(name: 'app_keyboard_index', methods: ['GET'])]
    public function index(KeyboardRepository $keyboardRepository): Response
    {
        return $this->render('keyboard/index.html.twig', [
            'keyboards' => $keyboardRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_keyboard_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $keyboard = new Keyboard();
        $form = $this->createForm(KeyboardType::class, $keyboard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // --- GESTION DE L'IMAGE ---
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            // Cette condition est nécessaire car le champ 'image' n'est pas requis
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On nettoie le nom du fichier (sécurité)
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // On déplace le fichier dans le dossier public/uploads
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion d'erreur si l'upload échoue
                }

                // On enregistre seulement le NOM du fichier dans la base de données
                $keyboard->setImage($newFilename);
            }
            // --- FIN GESTION IMAGE ---

            // IMPORTANT : On lie le clavier à l'inventaire existant (si ce n'est pas fait dans le form)
            // Pour l'instant, on peut récupérer le premier inventaire de la base pour tester
            // (Plus tard, on prendra celui de l'utilisateur connecté)
            // $inventory = $entityManager->getRepository(Inventory::class)->findOneBy([]);
            // $keyboard->setInventory($inventory);

            // Si ton formulaire inclut déjà le choix de l'inventaire, ignore les 2 lignes au-dessus.

            $entityManager->persist($keyboard);
            $entityManager->flush();

            return $this->redirectToRoute('app_keyboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('keyboard/new.html.twig', [
            'keyboard' => $keyboard,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_keyboard_show', methods: ['GET'])]
    public function show(Keyboard $keyboard): Response
    {
        return $this->render('keyboard/show.html.twig', [
            'keyboard' => $keyboard,
            'showcases' => $keyboard->getShowcases(),
        ]);
    }


    #[Route('/{id}/edit', name: 'app_keyboard_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Keyboard $keyboard, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(KeyboardType::class, $keyboard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- GESTION DE L'UPLOAD D'IMAGE (EDIT) ---
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            // On ne traite l'image que si un NOUVEAU fichier a été envoyé
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Sécurisation du nom du fichier
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Déplacement du fichier dans le dossier public/uploads
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Tu pourrais ajouter un message flash ici en cas d'erreur
                }

                // On met à jour la propriété 'image' de l'entité avec le nouveau nom
                $keyboard->setImage($newFilename);
            }
            // Si $imageFile est null, on ne fait rien : l'ancienne image reste en place.
            // ------------------------------------------

            $entityManager->flush();

            return $this->redirectToRoute('app_keyboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('keyboard/edit.html.twig', [
            'keyboard' => $keyboard,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_keyboard_delete', methods: ['POST'])]
    public function delete(Request $request, Keyboard $keyboard, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$keyboard->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($keyboard);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_keyboard_index', [], Response::HTTP_SEE_OTHER);
    }
}
