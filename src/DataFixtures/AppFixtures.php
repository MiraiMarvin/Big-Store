<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $monAdmin = new User();
        $monAdmin->setEmail('clerc.marvin@outlook.fr');
        $monAdmin->setNom('Clerc');
        $monAdmin->setPrenom('Marvin');
        $monAdmin->setPoints(15000);
        $monAdmin->setActif(true);
        $monAdmin->setRoles(['ROLE_ADMIN']);
        $monAdmin->setPassword($this->passwordHasher->hashPassword($monAdmin, 'Shambala123'));
        $manager->persist($monAdmin);

        $utilisateurs = [
            ['jean.dupont@test.fr', 'Dupont', 'Jean', 800],
            ['marie.martin@test.fr', 'Martin', 'Marie', 1200],
            ['pierre.durand@test.fr', 'Durand', 'Pierre', 600],
        ];

        foreach ($utilisateurs as [$email, $nom, $prenom, $points]) {
            $user = new User();
            $user->setEmail($email);
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setPoints($points);
            $user->setActif(true);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'test123'));
            $manager->persist($user);
        }

        $mesProduits = [
            ['iPhone 15 Pro', 'Électronique', 1199, 'Dernier iPhone Apple avec puce A17 Pro'],
            ['MacBook Air M2', 'Informatique', 1299, 'Ordinateur portable ultra-fin et puissant'],
            ['AirPods Pro', 'Audio', 279, 'Écouteurs sans fil avec réduction de bruit'],
            ['Livre PHP 8', 'Livres', 45, 'Guide complet pour apprendre PHP 8'],
            ['Symfony 6 Guide', 'Livres', 52, 'Maîtriser le framework Symfony 6'],
            ['T-shirt Dev', 'Vêtements', 25, 'T-shirt "Hello World" pour développeurs'],
            ['Mug Codeur', 'Accessoires', 15, 'Mug "Fuel by Coffee" pour programmeurs'],
            ['Clavier Mécanique', 'Informatique', 129, 'Clavier gaming RGB mécanique'],
            ['Souris Gaming', 'Informatique', 79, 'Souris haute précision pour gaming'],
            ['Écran 4K', 'Informatique', 399, 'Moniteur 27 pouces 4K HDR'],
        ];

        foreach ($mesProduits as [$nom, $categorie, $prix, $description]) {
            $produit = new Produit();
            $produit->setNom($nom);
            $produit->setCategory($categorie);
            $produit->setPrix($prix);
            $produit->setDescription($description);
            $produit->setCreatedBy($monAdmin);
            $manager->persist($produit);
        }

        $manager->flush();
    }
}