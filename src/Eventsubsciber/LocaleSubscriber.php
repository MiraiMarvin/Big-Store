<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        
        // Récupérer la locale de la session, défaut à 'fr'
        $sessionLocale = $request->getSession()->get('_locale', 'fr');
        
        // Valider la locale
        if (!in_array($sessionLocale, ['fr', 'en'])) {
            $sessionLocale = 'fr';
        }
        
        // Appliquer la locale à la requête
        $request->setLocale($sessionLocale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Priorité élevée pour s'exécuter avant d'autres listeners
            KernelEvents::REQUEST => [['onKernelRequest', 17]],
        ];
    }
}