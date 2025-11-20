<?php

namespace App\Controller;

use App\Entity\Keyboard;
use App\Entity\Showcase;
use App\Form\ShowcaseType;
use App\Repository\ShowcaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;


#[Route('/showcase')]
final class ShowcaseController extends AbstractController
{
    #[Route('/', name: 'app_showcase_index', methods: ['GET'])]
    public function index(ShowcaseRepository $showcaseRepository): Response
    {
        return $this->render('showcase/index.html.twig', [
            'showcases' => $showcaseRepository->findAll(),
        ]);
    }
    #[Route('/{showcase_id}/keyboard/{keyboard_id}', methods: ['GET'], name: 'app_showcase_keyboard_show')]
    public function keyboardShow(
        #[MapEntity(id: 'showcase_id')] Showcase $showcase,
        #[MapEntity(id: 'keyboard_id')] Keyboard $keyboard
    ): Response {
        // Étape 12: Vérification de cohérence (fortement recommandé) [29, 30]
        if (!$showcase->getKeyboards()->contains($keyboard)) {
            throw $this->createNotFoundException("Ce clavier n'est pas affiché dans cette Showcase!");
        }

        // NOTE: La vérification de si la Showcase est publiée sera ajoutée à l'étape 19

        return $this->render('showcase/keyboard_show.html.twig', [
            'keyboard' => $keyboard,
            'showcase' => $showcase, // On passe les deux objets à Twig
        ]);
    }

    #[Route('/new', name: 'app_showcase_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $showcase = new Showcase();
        $form = $this->createForm(ShowcaseType::class, $showcase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($showcase);
            $entityManager->flush();

            return $this->redirectToRoute('app_showcase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('showcase/new.html.twig', [
            'showcase' => $showcase,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_showcase_show', methods: ['GET'])]
    public function show(Showcase $showcase): Response
    {
        return $this->render('showcase/keyboard_show.html.twig', [
            'showcase' => $showcase,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_showcase_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Showcase $showcase, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShowcaseType::class, $showcase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_showcase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('showcase/edit.html.twig', [
            'showcase' => $showcase,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_showcase_delete', methods: ['POST'])]
    public function delete(Request $request, Showcase $showcase, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$showcase->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($showcase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_showcase_index', [], Response::HTTP_SEE_OTHER);
    }

}
