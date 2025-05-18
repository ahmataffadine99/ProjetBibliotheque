<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Genre;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LivreFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $genresReferences = ['science_fiction', 'fantasy', 'policier', 'romance', 'thriller', 'historique'];
        $auteursReferences = ['herbert', 'tolkien', 'christie', 'austen', 'king', 'zola'];

        for ($i = 0; $i < 10; $i++) {
            $livre = new Livre();
            $livre->setTitre($faker->sentence($faker->numberBetween(3, 6)));
            $livre->setDescription($faker->paragraph($faker->numberBetween(5, 15)));
            $livre->setDatePublication($faker->dateTimeBetween('-2 years', 'now'));

            // Ajouter des genres 
            for ($j = 0; $j < $faker->numberBetween(1, 3); $j++) {
                $nomGenre = $faker->randomElement($genresReferences);
                /** @var Genre|null $genre */
                $genre = $manager->getRepository(Genre::class)->findOneBy(['nom' => ucfirst(str_replace('_', '-', $nomGenre))]);
                if ($genre) {
                    $livre->addGenre($genre);
                }
            }

            // Ajouter des auteurs
            for ($j = 0; $j < $faker->numberBetween(1, 2); $j++) {
                $nomAuteurCle = $faker->randomElement($auteursReferences);
                $parts = explode('_', $nomAuteurCle);
                $nom = ucfirst($parts[0]);
                $prenom = isset($parts[1]) ? ucfirst($parts[1]) : null;
                /** @var Auteur|null $auteur */
                $auteur = $manager->getRepository(Auteur::class)->findOneBy(['nom' => $nom, 'prenom' => $prenom]);
                if ($auteur) {
                    $livre->addAuteur($auteur);
                }
            }

            $manager->persist($livre);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GenreFixture::class,
            AuteurFixture::class,
        ];
    }
}