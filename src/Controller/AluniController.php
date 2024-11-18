<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AluniController extends AbstractController {

    protected Connection $conn;

    public function __construct(
        protected ManagerRegistry $doctrine,
        protected EntityManagerInterface $em) {
        $this->conn = $em->getConnection();
    }
}
