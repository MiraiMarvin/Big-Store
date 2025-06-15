<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager
            ->getRepository(Produit::class)
            ->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $produit = $entityManager
            ->getRepository(Produit::class)
            ->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas.');
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    // Alternative avec ParamConverter (plus élégant)
    #[Route('/details/{id}', name: 'app_produit_details', methods: ['GET'])]
    public function details(Produit $produit): Response
    {
        // Symfony trouvera automatiquement le produit par son ID
        // et lancera une 404 si il n'existe pas
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }
    #[Route('/acheter/{id}', name: 'app_produit_acheter', methods: ['POST'])]
    public function acheter(int $id, EntityManagerInterface $entityManager): Response
    {
    $produit = $entityManager->getRepository(Produit::class)->find($id);

    if (!$produit) {
        throw $this->createNotFoundException('Produit non trouvé.');
    }

    $user = $this->getUser();

    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    if (!$user->isActif()) {
        $this->addFlash('danger', 'Votre compte est désactivé.');
        return $this->redirectToRoute('app_produit_show', ['id' => $id]);
    }

    if ($user->getPoints() < $produit->getPrix()) {
        $this->addFlash('danger', 'Vous n\'avez pas assez de points.');
        return $this->redirectToRoute('app_produit_show', ['id' => $id]);
    }

    // Déduction des points
    $user->setPoints($user->getPoints() - $produit->getPrix());

    // TODO : Ajouter la logique d'achat (ex : stocker l'achat en base)

    $entityManager->flush();

    $this->addFlash('success', 'Achat effectué avec succès !');
    return $this->redirectToRoute('app_produit_index');
    }

}