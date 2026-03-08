<?php

namespace App\Controller;

use App\Service\MeteoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MeteoController extends AbstractController
{
    #[Route('/meteo/{ville}', name: 'meteo_ville', methods: ['GET'])]
    public function meteoVille(string $ville, MeteoService $meteoService): JsonResponse
    {
        try {
            $data = $meteoService->getMeteo($ville);
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/meteo', name: 'meteo_user', methods: ['GET'])]
    public function meteoUser(MeteoService $meteoService): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $ville = $user->getVille();

        if (!$ville) {
            return $this->json(['error' => 'Aucune ville définie pour cet utilisateur'], 400);
        }

        try {
            $data = $meteoService->getMeteo($ville);
            return $this->json($data);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
