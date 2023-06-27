<?php

namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $type1=new Type(); 
        $type1->setDesignation("Particulier");
        $type1->setSysName('particular');
        $type2=new Type(); 
        $type2->setDesignation("Distributeur");
        $type2->setSysName('distributer');
        $type3=new Type(); 
        $type3->setDesignation("Partenaire");
        $type3->setSysName('partner');
        $type4=new Type(); 
        $type4->setDesignation("Administrateur");
        $type4->setSysName('admin');
        $type5=new Type(); 
        $type5->setDesignation("Point de vente fixe et mobile");
        $type5->setSysName('vendor');
        $type6=new Type(); 
        $type6->setDesignation("Entreprise");
        $type6->setSysName('enterprise');
        $manager->persist($type1);
        $manager->persist($type2);
        $manager->persist($type3);
        $manager->persist($type4);
        $manager->persist($type5);
        $manager->persist($type6);
        $manager->flush();
    }
}
