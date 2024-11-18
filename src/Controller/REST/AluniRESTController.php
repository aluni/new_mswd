<?php

namespace App\Controller\REST;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;

class AluniRESTController extends AbstractFOSRestController {

    protected Connection $conn;

    public function __construct(
            protected ManagerRegistry $doctrine,
            protected SerializerInterface $serializer,
            protected EntityManagerInterface $em) {
        $this->conn = $em->getConnection();
    }
}
