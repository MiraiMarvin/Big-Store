<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'app_change_locale')]
    public function changeLocale(string $locale, Request $request): Response
    {

        $allowedLocales = ['fr', 'en'];
        if (!in_array($locale, $allowedLocales)) {
            $locale = 'fr';
        }

        $request->getSession()->set('_locale', $locale);
        

        $request->setLocale($locale);

        if ($locale === 'fr') {
            $this->addFlash('success', 'Langue changée vers le français');
        } else {
            $this->addFlash('success', 'Language changed to English');
        }

        // Redirection intelligente
        $referer = $request->headers->get('referer');
        if ($referer && !str_contains($referer, 'change-locale')) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_home');
    }
}