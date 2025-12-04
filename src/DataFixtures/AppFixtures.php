<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use App\Entity\Enigma;
use App\Entity\Game;
use App\Entity\Setting;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@enigma.fr');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setVerified(true);
        $manager->persist($admin);

        $typePhoto = new Type();
        $typePhoto->setLabel('photo');
        $manager->persist($typePhoto);

        $typeTimeline = new Type();
        $typeTimeline->setLabel('timeline');
        $manager->persist($typeTimeline);

        $typeMcq = new Type();
        $typeMcq->setLabel('mcq');
        $manager->persist($typeMcq);

        for ($i = 1; $i <= 8; $i++) {
            $avatar = new Avatar();
            $avatar->setFilename("avatar{$i}.png");
            $manager->persist($avatar);
        }

        $game = new Game();
        $game->setTitle("L'Intelligence Artificielle");
        $game->setWelcomeMsg("Bienvenue dans ce jeu d'énigmes sur l'Intelligence Artificielle !\n\nDécouvrez les enjeux, les défis et les opportunités de cette technologie qui révolutionne notre monde.\n\nVous allez devoir résoudre plusieurs énigmes en équipe. Chaque énigme vous donnera un code secret. À la fin, vous devrez saisir le code final pour gagner.\n\nBonne chance !");
        $game->setWelcomeImg('ai-welcome.jpg');
        $manager->persist($game);

        $setting = new Setting();
        $setting->setGame($game);
        $setting->setGameStarted(false);
        $setting->setGameDuration(25);
        $setting->setFinalCode('VICTORY2024');
        $manager->persist($setting);

        $enigma = new Enigma();
        $enigma->setType($typePhoto);
        $enigma->setGame($game);
        $enigma->setOrder(1);
        $enigma->setTitle("IA ou pas ?");
        $enigma->setInstruction("Parmi chaque paire d'images, trouvez celle qui n'a PAS été générée par une Intelligence Artificielle. Vous avez droit à 2 erreurs maximum.");
        $enigma->setSecretCode('AI42');
        $enigma->setData([
            'pairs' => [
                [
                    'id' => 0,
                    'image1' => 'pair1-real.jpg',
                    'image2' => 'pair1-ai.jpg',
                    'correct' => 0
                ],
                [
                    'id' => 1,
                    'image1' => 'pair2-ai.jpg',
                    'image2' => 'pair2-real.jpg',
                    'correct' => 1
                ],
                [
                    'id' => 2,
                    'image1' => 'pair3-real.jpg',
                    'image2' => 'pair3-ai.jpg',
                    'correct' => 0
                ],
            ]
        ]);
        $manager->persist($enigma);

        $enigma2 = new Enigma();
        $enigma2->setType($typeTimeline);
        $enigma2->setGame($game);
        $enigma2->setOrder(2);
        $enigma2->setTitle("Histoire de l'IA");
        $enigma2->setInstruction("Remettez ces événements marquants de l'histoire de l'Intelligence Artificielle dans le bon ordre chronologique.");
        $enigma2->setSecretCode('CHRONO2024');
        $enigma2->setData([
            'items' => [
                ['id' => 1, 'text' => 'Test de Turing proposé par Alan Turing (1950)'],
                ['id' => 2, 'text' => 'Création du terme "Intelligence Artificielle" à Dartmouth (1956)'],
                ['id' => 3, 'text' => 'Deep Blue bat Kasparov aux échecs (1997)'],
                ['id' => 4, 'text' => 'Watson d\'IBM gagne à Jeopardy! (2011)'],
                ['id' => 5, 'text' => 'AlphaGo bat Lee Sedol au jeu de Go (2016)'],
            ]
        ]);
        $manager->persist($enigma2);

        $enigma3 = new Enigma();
        $enigma3->setType($typeMcq);
        $enigma3->setGame($game);
        $enigma3->setOrder(3);
        $enigma3->setTitle("Culture générale IA");
        $enigma3->setInstruction("Répondez correctement à toutes les questions sur l'Intelligence Artificielle.");
        $enigma3->setSecretCode('CULTURE42');
        $enigma3->setData([
            'questions' => [
                [
                    'question' => 'Qui est considéré comme le père de l\'Intelligence Artificielle ?',
                    'answers' => ['Alan Turing', 'Bill Gates', 'Steve Jobs', 'Mark Zuckerberg'],
                    'correct' => 0
                ],
                [
                    'question' => 'Que signifie "Machine Learning" ?',
                    'answers' => ['Apprendre aux machines', 'Apprentissage automatique', 'Réparation de machines', 'Programmation de robots'],
                    'correct' => 1
                ],
                [
                    'question' => 'Quel jeu AlphaGo a-t-il maîtrisé ?',
                    'answers' => ['Échecs', 'Poker', 'Go', 'Dames'],
                    'correct' => 2
                ],
            ]
        ]);
        $manager->persist($enigma3);

        $manager->flush();
    }
}
