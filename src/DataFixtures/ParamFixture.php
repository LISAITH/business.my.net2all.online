<?php

namespace App\DataFixtures;

use App\Entity\ParamProspections;
use App\Entity\ValueProspections;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParamFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $param1 = new ParamProspections();
        $param1->setLibelle("Rémunération par mois ");
        $param1->setCode("remu/M");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(50000);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Prospections  par jour ");
        $param1->setCode("pros/j");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(10);
        $manager->persist($param1);
        $manager->persist($valu1);



        $param1 = new ParamProspections();
        $param1->setLibelle("Montant par prospection supplementaire");
        $param1->setCode("montant/pros_suppl");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(500);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Objectif mensuel(propections validées)");
        $param1->setCode("objectif_mensuel");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(150);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Delai minimum de conversion (jours)");
        $param1->setCode("delai_conversion");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(30);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Commission avant le delai minimum de conversion (%)");
        $param1->setCode("commission_delai");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(10);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Commission après le delai  de conversion (%)");
        $param1->setCode("commission_apres_delai");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(5);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Prime première vente");
        $param1->setCode("prime_premiere_vente");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(10000);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Prime première vente Hebdo");
        $param1->setCode("prime_premiere_vente_hebdo");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(5000);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Prime Meilleure prospection du mois");
        $param1->setCode("prime_meilleur_pros");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(30000);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Prospection Supplémentaire et une conversion client( nombre) hebdo:");
        $param1->setCode("pros_suppl_hebdo");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(50);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Prime prospection Supplémentaire et une conversion client( nombre) hebdo:");
        $param1->setCode("prime_suppl_hebdo");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue(20000);
        $manager->persist($param1);
        $manager->persist($valu1);

        $param1 = new ParamProspections();
        $param1->setLibelle("Numéro ecash");
        $param1->setCode("ecash");
        $valu1 = new ValueProspections();
        $valu1->setParamProspection($param1);
        $valu1->setDoneAt(new DateTime());
        $valu1->setValue("fkgiuilihyygk");
        $manager->persist($param1);
        $manager->persist($valu1);



        $manager->flush();
    }
}
