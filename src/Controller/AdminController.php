<?php

namespace App\Controller;

use App\Entity\Enigma;
use App\Entity\Game;
use App\Entity\Setting;
use App\Form\EnigmaType;
use App\Form\GameType;
use App\Repository\EnigmaRepository;
use App\Repository\GameRepository;
use App\Repository\SettingRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_PROF')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/games', name: 'admin_games')]
    public function games(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();

        return $this->render('admin/games/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/games/create', name: 'admin_games_create')]
    public function createGame(Request $request, EntityManagerInterface $em): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $setting = new Setting();
            $setting->setGame($game);
            $setting->setFinalCode('');

            $em->persist($game);
            $em->persist($setting);
            $em->flush();

            $this->addFlash('success', 'Thème créé avec succès !');

            return $this->redirectToRoute('admin_games');
        }

        return $this->render('admin/games/form.html.twig', [
            'form' => $form,
            'title' => 'Créer un thème',
        ]);
    }

    #[Route('/games/{id}/edit', name: 'admin_games_edit')]
    public function editGame(Game $game, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Thème modifié avec succès !');

            return $this->redirectToRoute('admin_games');
        }

        return $this->render('admin/games/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier le thème',
            'game' => $game,
        ]);
    }

    #[Route('/games/{id}/delete', name: 'admin_games_delete', methods: ['POST'])]
    public function deleteGame(Game $game, EntityManagerInterface $em): Response
    {
        $em->remove($game);
        $em->flush();

        $this->addFlash('success', 'Thème supprimé avec succès !');

        return $this->redirectToRoute('admin_games');
    }

    #[Route('/game/{id}/enigmas', name: 'admin_enigmas')]
    public function enigmas(Game $game, EnigmaRepository $enigmaRepository): Response
    {
        $enigmas = $enigmaRepository->findBy(['game' => $game], ['order' => 'ASC']);

        return $this->render('admin/enigmas/index.html.twig', [
            'game' => $game,
            'enigmas' => $enigmas,
        ]);
    }

    #[Route('/game/{gameId}/enigmas/create', name: 'admin_enigmas_create')]
    public function createEnigma(int $gameId, Request $request, EntityManagerInterface $em, GameRepository $gameRepository): Response
    {
        $game = $gameRepository->find($gameId);
        if (!$game) {
            throw $this->createNotFoundException('Jeu introuvable');
        }

        $enigma = new Enigma();
        $enigma->setGame($game);
        $form = $this->createForm(EnigmaType::class, $enigma);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataString = $form->get('data')->getData();
            $enigma->setData(json_decode($dataString, true) ?: []);

            $em->persist($enigma);
            $em->flush();

            $this->addFlash('success', 'Énigme créée avec succès !');

            return $this->redirectToRoute('admin_enigmas', ['id' => $gameId]);
        }

        return $this->render('admin/enigmas/form.html.twig', [
            'form' => $form,
            'title' => 'Créer une énigme',
            'game' => $game,
        ]);
    }

    #[Route('/enigmas/{id}/edit', name: 'admin_enigmas_edit')]
    public function editEnigma(Enigma $enigma, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EnigmaType::class, $enigma);
        $form->get('data')->setData(json_encode($enigma->getData(), JSON_PRETTY_PRINT));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataString = $form->get('data')->getData();
            $enigma->setData(json_decode($dataString, true) ?: []);

            $em->flush();

            $this->addFlash('success', 'Énigme modifiée avec succès !');

            return $this->redirectToRoute('admin_enigmas', ['id' => $enigma->getGame()->getId()]);
        }

        return $this->render('admin/enigmas/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier l\'énigme',
            'enigma' => $enigma,
            'game' => $enigma->getGame(),
        ]);
    }

    #[Route('/enigmas/{id}/delete', name: 'admin_enigmas_delete', methods: ['POST'])]
    public function deleteEnigma(Enigma $enigma, EntityManagerInterface $em): Response
    {
        $gameId = $enigma->getGame()->getId();
        $em->remove($enigma);
        $em->flush();

        $this->addFlash('success', 'Énigme supprimée avec succès !');

        return $this->redirectToRoute('admin_enigmas', ['id' => $gameId]);
    }

    #[Route('/session', name: 'admin_session')]
    public function session(GameRepository $gameRepository, SettingRepository $settingRepository, TeamRepository $teamRepository): Response
    {
        $games = $gameRepository->findAll();
        $game = $games[0] ?? null;

        if (!$game) {
            $this->addFlash('error', 'Aucun jeu disponible');
            return $this->redirectToRoute('admin_dashboard');
        }

        $setting = $settingRepository->findOneBy(['game' => $game]);
        if (!$setting) {
            $setting = new Setting();
            $setting->setGame($game);
        }

        $teams = $teamRepository->findBy(['game' => $game], ['name' => 'ASC']);

        return $this->render('admin/session/index.html.twig', [
            'game' => $game,
            'setting' => $setting,
            'teams' => $teams,
        ]);
    }

    #[Route('/session/start', name: 'admin_session_start', methods: ['POST'])]
    public function startGame(Request $request, EntityManagerInterface $em, GameRepository $gameRepository, SettingRepository $settingRepository): Response
    {
        $games = $gameRepository->findAll();
        $game = $games[0] ?? null;

        if (!$game) {
            $this->addFlash('error', 'Aucun jeu disponible');
            return $this->redirectToRoute('admin_dashboard');
        }

        $setting = $settingRepository->findOneBy(['game' => $game]);
        if (!$setting) {
            $setting = new Setting();
            $setting->setGame($game);
            $em->persist($setting);
        }

        $finalCode = $request->request->get('finalCode', '');
        $setting->setFinalCode($finalCode);
        $setting->setGameStarted(true);
        $setting->setStartedAt(new \DateTime());

        $em->flush();

        $this->addFlash('success', 'Partie lancée avec succès !');

        return $this->redirectToRoute('admin_session');
    }

    #[Route('/session/stop', name: 'admin_session_stop', methods: ['POST'])]
    public function stopGame(EntityManagerInterface $em, GameRepository $gameRepository, SettingRepository $settingRepository): Response
    {
        $games = $gameRepository->findAll();
        $game = $games[0] ?? null;

        if (!$game) {
            $this->addFlash('error', 'Aucun jeu disponible');
            return $this->redirectToRoute('admin_dashboard');
        }

        $setting = $settingRepository->findOneBy(['game' => $game]);
        if ($setting) {
            $setting->setGameStarted(false);
            $em->flush();
        }

        $this->addFlash('success', 'Partie terminée avec succès !');

        return $this->redirectToRoute('admin_session');
    }

    #[Route('/session/teams-progress', name: 'admin_teams_progress')]
    public function getTeamsProgress(TeamRepository $teamRepository, GameRepository $gameRepository): JsonResponse
    {
        $games = $gameRepository->findAll();
        $game = $games[0] ?? null;

        if (!$game) {
            return new JsonResponse(['teams' => []]);
        }

        $teams = $teamRepository->findBy(['game' => $game]);
        $teamsData = [];

        foreach ($teams as $team) {
            $elapsed = '';
            if ($team->getStartedAt()) {
                $endTime = $team->getFinishedAt() ?: new \DateTime();
                $diff = $team->getStartedAt()->diff($endTime);
                $elapsed = $diff->format('%i:%S');
            }

            $teamsData[] = [
                'name' => $team->getName(),
                'currentEnigma' => $team->getCurrentEnigma(),
                'elapsed' => $elapsed,
                'finished' => $team->getFinishedAt() !== null,
                'position' => $team->getPosition(),
            ];
        }

        return new JsonResponse(['teams' => $teamsData]);
    }
}
