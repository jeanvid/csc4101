<?php

namespace App\DataFixtures;

use App\Entity\Inventory;
use App\Entity\Keyboard;
use App\Entity\Member;
use App\Entity\Showcase;
use App\Entity\User; // Ajout de l'entité User pour la connexion
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Indispensable pour le hash

class AppFixtures extends Fixture
{
    // On déclare une propriété pour stocker le service de hachage
    private UserPasswordHasherInterface $hasher;

    // On demande à Symfony de nous donner (injecter) ce service
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // --- 1. CRÉATION DE L'UTILISATEUR (Pour se connecter) ---
        $user = new User();
        $user->setEmail('jean@test.com');

        // On hache le mot de passe "password" pour la sécurité
        $hashedPassword = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        // $user->setRoles(['ROLE_USER']); // Optionnel, par défaut ROLE_USER
        $manager->persist($user);


        // --- 2. CRÉATION DU MEMBRE (Pour les données) ---
        $member = new Member();

        // [IMPORTANT] : Idéalement, il faudra lier Member et User ici plus tard.
        // Ex: $member->setUser($user); ou $user->setMember($member);
        // Pour l'instant, on les crée séparément pour ne pas bloquer.

        $manager->persist($member);


        // --- 3. CRÉATION DE L'INVENTAIRE (Lié au Membre) ---
        $inventory = new Inventory();
        $inventory->setDescription('Inventaire Principal : Collection de claviers custom');

        // On utilise la relation corrigée précédemment (Inventory -> Member)
        $inventory->setMember($member);

        // Double liaison si ton code l'exige (Member -> Inventory)
        $member->setInventory($inventory);

        $manager->persist($inventory);


        // --- 4. CRÉATION DES CLAVIERS ---
        $claviersData = [
            ['Keychron Q1', 'Keychron', 'Gateron Pro Red', 'PBT Double Shot'],
            ['GMMK Pro', 'Glorious', 'Glorious Panda', 'Aura Keycaps'],
            ['Tofu65', 'KBDFans', 'Tealios V2', 'GMK Laser'],
            ['Anne Pro 2', 'Obinslab', 'Kailh Box White', 'Stock']
        ];

        foreach ($claviersData as $data) {
            $kb = new Keyboard();
            $kb->setName($data[0]);
            $kb->setBrand($data[1]);
            $kb->setSwitchType($data[2]);
            $kb->setKeycapSet($data[3]);
            $kb->setDescription("Un clavier " . $data[1] . " custom.");

            // Liaison Clavier -> Inventaire
            $kb->setInventory($inventory);

            $manager->persist($kb);
        }


        // --- 5. LA SHOWCASE ---
        $showcase = new Showcase();
        $showcase->setName("Mes favoris 2025");
        $showcase->setDescription("Une sélection de mes meilleurs builds.");
        $showcase->setPublished(true);

        // Liaison Showcase -> Member
        $member->addShowcase($showcase);

        $manager->persist($showcase);


        // --- 6. ENVOI EN BASE DE DONNÉES ---
        $manager->flush();
    }
}
