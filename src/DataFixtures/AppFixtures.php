<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Conseil;
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
        /**
         * UTILISATEURS
         */

        // Admin
        $admin = new User();
        $admin->setLogin("admin");
        $admin->setVille("Paris");
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, "admin123")
        );
        $manager->persist($admin);

        // User 1
        $user1 = new User();
        $user1->setLogin("jardinier1");
        $user1->setVille("Lyon");
        $user1->setRoles(["ROLE_USER"]);
        $user1->setPassword(
            $this->passwordHasher->hashPassword($user1, "password1")
        );
        $manager->persist($user1);

        // User 2
        $user2 = new User();
        $user2->setLogin("jardinier2");
        $user2->setVille("Marseille");
        $user2->setRoles(["ROLE_USER"]);
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, "password2")
        );
        $manager->persist($user2);


        /**
         * CONSEILS
         */

        $conseils = [
            [
                "contenu" => "Arrosez vos plantes tôt le matin pour limiter l'évaporation.",
                "mois" => [6, 7, 8], // Été
            ],
            [
                "contenu" => "Plantez vos tomates après les dernières gelées.",
                "mois" => [4, 5], // Printemps
            ],
            [
                "contenu" => "Taillez vos arbres fruitiers en hiver pour favoriser la croissance.",
                "mois" => [1, 2], // Hiver
            ],
            [
                "contenu" => "Paillez le sol pour conserver l'humidité.",
                "mois" => [5, 6, 7, 8],
            ],
            [
                "contenu" => "Semez les radis toutes les deux semaines pour une récolte continue.",
                "mois" => [3, 4, 5],
            ],
            [
                "contenu" => "Protégez vos plantes du gel avec un voile d'hivernage.",
                "mois" => [11, 12, 1],
            ],
        ];

        foreach ($conseils as $data) {
            $conseil = new Conseil();
            $conseil->setContenu($data["contenu"]);
            $conseil->setMois($data["mois"]);
            $conseil->setCreatedAt(new \DateTime());
            $manager->persist($conseil);
        }

        $manager->flush();
    }
}
