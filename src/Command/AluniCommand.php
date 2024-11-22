<?php

namespace App\Command;

use AllowDynamicProperties;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Comando base
 *
 * @author Álvaro Peláez Santana
 * @copyright ALUNI ALOJAMIENTOS S.L.
 */
#[AllowDynamicProperties]
#[AsCommand(name: 'aluni:comando-base')]
class AluniCommand extends Command {

    public function __construct(protected EntityManagerInterface $em,
                                protected Connection $conn,
                                protected ParameterBagInterface $params,
                                #[Autowire(service: 'fos_user.util.user_manipulator')]
                                protected $userManipulator,
                                protected RouterInterface $router) {
        parent::__construct();
    }
}
