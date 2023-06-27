<?php

namespace App\Twig;

use App\Entity\User;
use Twig\TwigFilter;
use Twig\TwigFunction;

use App\Entity\ProfilProspections;
use Twig\Extension\AbstractExtension;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ProfilProspectionsRepository;

class MyExtentionExtension extends AbstractExtension
{  
    protected $registry;
    public function __construct(ManagerRegistry $registry)
    {
        $this -> registry= $registry;
    }


    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('profil_prospection', [$this, 'profil_prospection']),
            new TwigFunction('profil_check', [$this, 'profil_check']),
            new TwigFunction('profil_adder', [$this, 'profil_adder']),
        ];
    }

    public function doSomething($value)
    {
        // ...
    }

    public function profil_prospection($id)
    {   
        $profilProspectionsRepository = $this->registry->getRepository(ProfilProspections::class);
        return $profilProspectionsRepository->find($id)->getLibelle();
    }
    public function profil_check( User $user )
    {   
        $parent = $user->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        return ($userProfil ) ;
    }
    public function profil_adder( User $user )
    {   
        $parent = $user->getParticuliers()[0];
        $userProfil =$parent->getProfilId();
        return ($userProfil && $userProfil !=1) ;
    }
}
