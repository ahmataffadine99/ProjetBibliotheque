<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AuteurFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $auteurs = [
            ['nom' => 'Herbert', 'prenom' => 'Frank'],
            ['nom' => 'Tolkien', 'prenom' => 'J.R.R.'],
            ['nom' => 'Christie', 'prenom' => 'Agatha'],
            ['nom' => 'Austen', 'prenom' => 'Jane'],
            ['nom' => 'King', 'prenom' => 'Stephen'],
            ['nom' => 'Zola', 'prenom' => 'Émile'],
        ];

        foreach ($auteurs as $auteurData) {
            $auteur = new Auteur();
            $auteur->setNom($auteurData['nom']);
            $auteur->setPrenom($auteurData['prenom']);
            $manager->persist($auteur);
            $this->addReference('auteur_' . strtolower(str_replace(' ', '_', $auteurData['nom'])), $auteur);
        }

        $manager->flush();
    }
}