<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $genres = ['Science-fiction', 'Fantasy', 'Policier', 'Romance', 'Thriller', 'Historique'];

        foreach ($genres as $nomGenre) {
            $genre = new Genre();
            $genre->setNom($nomGenre);
            $manager->persist($genre);
            $this->addReference('genre_' . strtolower(str_replace('-', '_', $nomGenre)), $genre);
        }

        $manager->flush();
    }
}