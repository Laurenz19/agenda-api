<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Candidat;
use App\Entity\Entretien;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    { $faker = Factory::create();

        for($c = 0; $c < 10; $c ++){
               
            $candidat = new Candidat();
            $candidat->setNom($faker->name)
                     ->setTel($faker->phoneNumber)
                     ->setEmail($faker->email)
                     ->setCompetences(["Vuejs", "Symfony"])
                     ->setDiplome("Master");

                     $manager->persist($candidat);

                     $entretien = new Entretien();
                     $entretien->setDate(new DateTime($faker->date))
                               ->setHeure($faker->time) 
                               ->setLieu("tanambao")
                               ->setCandidat($candidat)
                               ->setTitre("Entretien d'embauche");               
            
            $manager->persist($entretien);         
        }

        $manager->flush();

    }
}
