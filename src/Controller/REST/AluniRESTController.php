<?php

namespace App\Controller\REST;

use App\Services\SeguridadService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

class AluniRESTController extends AbstractFOSRestController {

    protected Connection $conn;

    public function __construct(
            protected ManagerRegistry $doctrine,
            protected SerializerInterface $serializer,
            protected EntityManagerInterface $em,
            protected SeguridadService $seguridad) {
        $this->conn = $em->getConnection();
    }
}
