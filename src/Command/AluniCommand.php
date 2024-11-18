<?php

namespace App\Command;

use AllowDynamicProperties;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Comando base
 *
 * @author Álvaro Peláez Santana
 * @copyright ALUNI ALOJAMIENTOS S.L.
 */
#[AllowDynamicProperties] class AluniCommand extends Command {
    protected Connection $conn;
    protected static $defaultName = 'aluni:comando-base';


    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params) {
        $this->conn = $em->getConnection();
        $this->em = $em;
        $this->params = $params;
        parent::__construct();
    }
}
