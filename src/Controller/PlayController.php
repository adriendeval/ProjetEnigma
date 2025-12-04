<?php

namespace App\Controller;

use App\Entity\Team;
use App\Repository\EnigmaRepository;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/play')]
class PlayController extends AbstractController
{
    #[Route('/', name: 'play_index')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $teamId = $request->getSession()->get('team_id');
        if (!$teamId) {
            return $this->redirectToRoute('home');
        }

        $team = $em->getRepository(Team::class)->find($teamId);
        if (!$team) {
            return $this->redirectToRoute('home');
        }

        $game = $team->getGame();
        $setting = $game->getSetting();

        if (!$setting || !$setting->isGameStarted()) {
            return $this->redirectToRoute('waiting');
        }

        return $this->render('play/index.html.twig', [
            'team' => $team,
            'game' => $game,
            'setting' => $setting,
        ]);
    }

    #[Route('/enigma/{order}', name: 'play_enigma')]
    public function enigma(int $order, Request $request, EntityManagerInterface $em, EnigmaRepository $enigmaRepository): Response
    {
        $teamId = $request->getSession()->get('team_id');
        if (!$teamId) {
            return $this->redirectToRoute('home');
        }

        $team = $em->getRepository(Team::class)->find($teamId);
        if (!$team) {
            return $this->redirectToRoute('home');
        }

        $game = $team->getGame();
        $setting = $game->getSetting();

        if (!$setting || !$setting->isGameStarted()) {
            return $this->redirectToRoute('waiting');
        }

        $enigma = $enigmaRepository->findOneBy([
            'game' => $game,
            'order' => $order
        ]);

        if (!$enigma) {
            throw $this->createNotFoundException('Énigme introuvable');
        }

        return $this->render('play/enigma.html.twig', [
            'team' => $team,
            'enigma' => $enigma,
            'setting' => $setting,
        ]);
    }

    #[Route('/validate-enigma', name: 'play_validate_enigma', methods: ['POST'])]
    public function validateEnigma(Request $request, EntityManagerInterface $em, EnigmaRepository $enigmaRepository): JsonResponse
    {
        $teamId = $request->getSession()->get('team_id');
        if (!$teamId) {
            return new JsonResponse(['success' => false, 'message' => 'Équipe introuvable']);
        }

        $team = $em->getRepository(Team::class)->find($teamId);
        if (!$team) {
            return new JsonResponse(['success' => false, 'message' => 'Équipe introuvable']);
        }

        $data = json_decode($request->getContent(), true);
        $enigmaId = $data['enigmaId'] ?? null;
        $answer = $data['answer'] ?? null;

        if (!$enigmaId) {
            return new JsonResponse(['success' => false, 'message' => 'Énigme non spécifiée']);
        }

        $enigma = $enigmaRepository->find($enigmaId);
        if (!$enigma) {
            return new JsonResponse(['success' => false, 'message' => 'Énigme introuvable']);
        }

        $isValid = $this->validateAnswer($enigma, $answer);

        if ($isValid) {
            $team->setCurrentEnigma($team->getCurrentEnigma() + 1);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'secretCode' => $enigma->getSecretCode(),
                'message' => 'Bonne réponse !'
            ]);
        }

        return new JsonResponse(['success' => false, 'message' => 'Mauvaise réponse']);
    }

    #[Route('/finish', name: 'play_finish')]
    public function finish(Request $request, EntityManagerInterface $em): Response
    {
        $teamId = $request->getSession()->get('team_id');
        if (!$teamId) {
            return $this->redirectToRoute('home');
        }

        $team = $em->getRepository(Team::class)->find($teamId);
        if (!$team) {
            return $this->redirectToRoute('home');
        }

        $game = $team->getGame();
        $setting = $game->getSetting();

        if (!$setting || !$setting->isGameStarted()) {
            return $this->redirectToRoute('waiting');
        }

        return $this->render('play/finish.html.twig', [
            'team' => $team,
            'setting' => $setting,
        ]);
    }

    #[Route('/validate-final-code', name: 'play_validate_final_code', methods: ['POST'])]
    public function validateFinalCode(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $teamId = $request->getSession()->get('team_id');
        if (!$teamId) {
            return new JsonResponse(['success' => false, 'message' => 'Équipe introuvable']);
        }

        $team = $em->getRepository(Team::class)->find($teamId);
        if (!$team) {
            return new JsonResponse(['success' => false, 'message' => 'Équipe introuvable']);
        }

        $data = json_decode($request->getContent(), true);
        $finalCode = $data['finalCode'] ?? null;

        $setting = $team->getGame()->getSetting();
        if ($setting && $finalCode === $setting->getFinalCode()) {
            if (!$team->getFinishedAt()) {
                $team->setFinishedAt(new \DateTime());
                $em->flush();

                $finishedTeams = $em->getRepository(Team::class)->createQueryBuilder('t')
                    ->where('t.game = :game')
                    ->andWhere('t.finishedAt IS NOT NULL')
                    ->setParameter('game', $team->getGame())
                    ->orderBy('t.finishedAt', 'ASC')
                    ->getQuery()
                    ->getResult();

                foreach ($finishedTeams as $index => $finishedTeam) {
                    $finishedTeam->setPosition($index + 1);
                }
                $em->flush();
            }

            $duration = $team->getStartedAt()->diff($team->getFinishedAt());
            $durationString = $duration->format('%i minutes %s secondes');

            return new JsonResponse([
                'success' => true,
                'message' => 'Félicitations ! Vous avez terminé !',
                'duration' => $durationString,
                'position' => $team->getPosition()
            ]);
        }

        return new JsonResponse(['success' => false, 'message' => 'Code incorrect']);
    }

    private function validateAnswer($enigma, $answer): bool
    {
        $type = $enigma->getType()->getLabel();
        $data = $enigma->getData();

        switch ($type) {
            case 'photo':
                return $this->validatePhotoAnswer($data, $answer);
            case 'timeline':
                return $this->validateTimelineAnswer($data, $answer);
            case 'mcq':
                return $this->validateMcqAnswer($data, $answer);
            default:
                return false;
        }
    }

    private function validatePhotoAnswer($data, $answer): bool
    {
        if (!isset($data['pairs']) || !is_array($answer)) {
            return false;
        }

        foreach ($data['pairs'] as $index => $pair) {
            if (!isset($answer[$index]) || $answer[$index] !== $pair['correct']) {
                return false;
            }
        }

        return true;
    }

    private function validateTimelineAnswer($data, $answer): bool
    {
        if (!isset($data['items']) || !is_array($answer)) {
            return false;
        }

        $correctOrder = array_column($data['items'], 'id');
        return $answer === $correctOrder;
    }

    private function validateMcqAnswer($data, $answer): bool
    {
        if (!isset($data['questions']) || !is_array($answer)) {
            return false;
        }

        foreach ($data['questions'] as $index => $question) {
            if (!isset($answer[$index]) || $answer[$index] !== $question['correct']) {
                return false;
            }
        }

        return true;
    }
}
