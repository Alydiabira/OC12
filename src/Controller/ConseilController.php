<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Repository\ConseilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ConseilController extends AbstractController
{
    #[Route('/conseil/{mois}', name: 'conseil_par_mois', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getConseilsParMois(int $mois, ConseilRepository $repo): JsonResponse
    {
        if ($mois < 1 || $mois > 12) {
            return new JsonResponse(['error' => 'Mois invalide'], 400);
        }

        $conseils = $repo->findByMonth($mois);

        return new JsonResponse($conseils);
    }

    #[Route('/conseil', name: 'conseil_mois_courant', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getConseilsMoisCourant(ConseilRepository $repo): JsonResponse
    {
        $mois = (int) date('n');
        $conseils = $repo->findByMonth($mois);

        return new JsonResponse($conseils);
    }

    #[Route('/conseil', name: 'conseil_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createConseil(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['contenu'], $data['mois'])) {
            return new JsonResponse(['error' => 'Champs manquants'], 400);
        }

        if (!is_array($data['mois'])) {
            return new JsonResponse(['error' => 'Le champ mois doit être un tableau'], 400);
        }

        foreach ($data['mois'] as $m) {
            if ($m < 1 || $m > 12) {
                return new JsonResponse(['error' => 'Mois invalide'], 400);
            }
        }

        $conseil = new Conseil();
        $conseil->setContenu($data['contenu']);
        $conseil->setMois($data['mois']);
        $conseil->setCreatedAt(new \DateTime());

        $em->persist($conseil);
        $em->flush();

        return new JsonResponse(['message' => 'Conseil créé'], 201);
    }

    #[Route('/conseil/{id}', name: 'conseil_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateConseil(int $id, Request $request, ConseilRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $conseil = $repo->find($id);

        if (!$conseil) {
            return new JsonResponse(['error' => 'Conseil introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['contenu'])) {
            $conseil->setContenu($data['contenu']);
        }

        if (isset($data['mois'])) {
            if (!is_array($data['mois'])) {
                return new JsonResponse(['error' => 'Le champ mois doit être un tableau'], 400);
            }

            foreach ($data['mois'] as $m) {
                if ($m < 1 || $m > 12) {
                    return new JsonResponse(['error' => 'Mois invalide'], 400);
                }
            }

            $conseil->setMois($data['mois']);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Conseil mis à jour']);
    }

    #[Route('/conseil/{id}', name: 'conseil_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteConseil(int $id, ConseilRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $conseil = $repo->find($id);

        if (!$conseil) {
            return new JsonResponse(['error' => 'Conseil introuvable'], 404);
        }

        $em->remove($conseil);
        $em->flush();

        return new JsonResponse(['message' => 'Conseil supprimé']);
    }
}
