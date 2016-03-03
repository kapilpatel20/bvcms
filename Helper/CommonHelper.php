<?php
namespace Disney\AdminBundle\Helper;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class CommonHelper {

    private $entityManager;

    public function __construct(EntityManager $entityManager, Session $session) {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public function pr($object, $exit=0 ) {
        echo "<pre>";
        print_r($object);
        if($exit == 1)
            exit;
    }
    
}

