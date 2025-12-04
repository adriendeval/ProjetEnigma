<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\GameRepository;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();
        $game = $games[0] ?? null;

        if (!$game) {
            throw $this->createNotFoundException('Aucun jeu disponible');
        }

        $team = new Team();
        $team->setGame($game);
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($team);
            $em->flush();

            $response = $this->redirectToRoute('waiting');
            $response->headers->setCookie(new Cookie('team_id', $team->getId(), strtotime('+1 year')));

            return $response;
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
            'game' => $game,
        ]);
    }

    #[Route('/waiting', name: 'waiting')]
    public function waiting(Request $request, EntityManagerInterface $em): Response
    {
        $teamId = $request->cookies->get('team_id');
        if (!$teamId) {
            return $this->redirectToRoute('home');
        }

        $team = $em->getRepository(Team::class)->find($teamId);
        if (!$team) {
            return $this->redirectToRoute('home');
        }

        return $this->render('home/waiting.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/check-game-started', name: 'check_game_started')]
    public function checkGameStarted(Request $request, EntityManagerInterface $em, SettingRepository $settingRepository): JsonResponse
    {
        $teamId = $request->cookies->get('team_id');
        if (!$teamId) {
            return new JsonResponse(['started' => false]);
        }

        $team = $em->getRepository(Team::class)->find($teamId);
        if (!$team) {
            return new JsonResponse(['started' => false]);
        }

        $setting = $settingRepository->findOneBy(['game' => $team->getGame()]);
        if ($setting && $setting->isGameStarted()) {
            if (!$team->getStartedAt()) {
                $team->setStartedAt(new \DateTime());
                $em->flush();
            }
            return new JsonResponse(['started' => true]);
        }

        return new JsonResponse(['started' => false]);
    }
}
