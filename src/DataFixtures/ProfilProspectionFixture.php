<?php

namespace App\DataFixtures;

use App\Entity\ProfilProspections;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProfilProspectionFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        
        $profil1  = new ProfilProspections();
        $profil1->setLibelle("Brand Advocates");
        $manager->persist($profil1);

        $profil2  = new ProfilProspections();
        $profil2->setLibelle("Brands Managers");
        $manager->persist($profil2);

        $profil3  = new ProfilProspections();
        $profil3 ->setLibelle("Market Chiefs");
        $manager->persist($profil3);

        $profil4  = new ProfilProspections();
        $profil4 ->setLibelle("Market Administrators");
        $manager->persist($profil4);

        $profil5  = new ProfilProspections();
        $profil5 ->setLibelle("Market Managers");
        $manager->persist($profil5);
        

        $manager->flush();
    }
}
