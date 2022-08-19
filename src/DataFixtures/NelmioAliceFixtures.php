<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\DataFixtures\EchoNativeLoader;

class NelmioAliceFixtures extends Fixture
{
    public function load(ObjectManager $em)
    {
        $loader = new EchoNativeLoader();
        
        //importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__.'/fixtures.yaml')->getObjects();

        // Persiste chacun des objets à enregistrer en BDD
        foreach ($entities as $entity) {
            $em->persist($entity);
        };
        
        //enregistre
        $em->flush();
    }
}
