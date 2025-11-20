<?php

namespace App\Controller;

use App\Entity\Inventory;
use App\Entity\User;
use App\Repository\InventoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends AbstractController
{
    /**
     * Affiche la liste de TOUS les inventaires (Page d'accueil)
     */
    #[Route('/', name: 'inventory_list')]
    public function list(InventoryRepository $inventoryRepository): Response
    {
        return $this->render('inventory/index.html.twig', [
            // Attention : Assure-toi que ton fichier template s'appelle bien index.html.twig ou list.html.twig
            // Si tu as gardé le nom généré par make:crud, c'est souvent index.html.twig
            'inventories' => $inventoryRepository->findAll(),
        ]);
    }

    /**
     * ÉTAPE 19 : Affiche UNIQUEMENT l'inventaire de l'utilisateur connecté
     * Cette route doit être placée AVANT la route /{id} pour ne pas être confondue avec un ID
     */
    #[Route('/inventory/mine', name: 'app_my_inventory', methods: ['GET'])]
    public function myInventory(): Response
    {
        // 1. Récupérer l'utilisateur connecté (via le système de sécurité)
        /** @var User $user */
        $user = $this->getUser();

        // Si personne n'est connecté, on redirige vers le login
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // 2. Récupérer le membre lié
        // NOTE : Si $user->getMember() est souligné en rouge, c'est qu'il faut générer les getters dans User.php
        $member = $user->getMember();

        if (!$member) {
            // Cas rare : Utilisateur connecté mais pas de profil membre associé
            // On pourrait rediriger vers une page de création de profil
            throw $this->createNotFoundException("Profil membre introuvable pour cet utilisateur.");
        }

        // 3. Récupérer l'inventaire du membre
        $inventory = $member->getInventory();

        if (!$inventory) {
            // Le membre existe mais n'a pas encore créé d'inventaire
            return $this->render('inventory/empty.html.twig'); // Tu devras peut-être créer cette vue simple
        }

        // 4. On réutilise la vue "show" standard, mais avec les données de l'utilisateur connecté
        return $this->render('inventory/show.html.twig', [
            'inventory' => $inventory,
            'is_my_inventory' => true, // Petite astuce pour afficher des boutons "Modifier" dans la vue plus tard
        ]);
    }

    /**
     * Affiche un inventaire spécifique via son ID
     */
    #[Route('/inventory/{id}', name: 'inventory_show', requirements: ['id' => '\d+'])]
    public function show(Inventory $inventory): Response
    {
        // Symfony fait le "find($id)" automatiquement grâce au typage (Inventory $inventory)

        // CORRECTION : On affiche la vue de l'INVENTAIRE, pas d'un clavier
        // Et on passe l'objet inventory complet pour pouvoir boucler sur ses claviers dans Twig
        return $this->render('inventory/show.html.twig', [
            'inventory' => $inventory,
            'is_my_inventory' => false,
        ]);
    }
}