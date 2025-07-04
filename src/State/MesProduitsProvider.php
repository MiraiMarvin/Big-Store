<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ProduitRepository;
use Symfony\Component\Security\Core\Security;

final class MesProduitsProvider implements ProviderInterface
{
    public function __construct(
        private ProduitRepository $produitRepository,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return [];
        }

        return $this->produitRepository->findBy(['createdBy' => $user]);
    }
}