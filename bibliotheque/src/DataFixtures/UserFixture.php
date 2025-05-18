<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
      
        $admin = new User();
        $admin->setEmail('admin@bibliotheque.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setNom('Super');
        $admin->setPrenom('Admin');
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@bibliotheque.fr');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user456'));
        $user->setNom('Vasseur');
        $user->setPrenom('Monsieur');
        $manager->persist($user);


        // Création d'un utilisateur avec un bon mail pour tester la réinitialisation de mot de passe
        $resetUser = new User();
        $resetUser->setEmail('ahmataffadine82@gmail.com');
        $resetUser->setRoles(['ROLE_USER']);
        $resetUser->setPassword($this->passwordHasher->hashPassword($resetUser, 'user123'));
        $resetUser->setNom('Ahmat');
        $resetUser->setPrenom('Affadine');
        $manager->persist($resetUser);


        $banned = new User();
        $banned->setEmail('banned@bibliotheque.fr');
        $banned->setRoles(['ROLE_BANNED']);
        $banned->setPassword($this->passwordHasher->hashPassword($banned, 'banned789'));
        $banned->setNom('Utilisateur');
        $banned->setPrenom('Banni');
        $manager->persist($banned);

        $manager->flush();
    }
}