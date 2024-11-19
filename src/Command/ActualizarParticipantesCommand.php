<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Participante;

/**
 * Actualiza los entrada de actividades desde Blogger. Este comando hace una petición HTTP a la API 
 * de Blogger de Google para pedir un listado de entradas en formato json desde la fecha que 
 * indiquemos. Estas entradas de actividades se guardarán en una carpeta del proyecto definida por
 * configuración, y serán las que se muestren en la web.
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class ActualizarParticipantesCommand extends AluniCommand {

    /**
     * El comando recibe un argumento que es opcional(fecha), en caso de que este argumento no se
     * incluya se entenderá que se quieren pedir todas las entradas del último año. La opción de 
     * mantener anteriores mantiene los entrada ya existentes que hayamos importado en ocasiones
     * anteriores, en caso de no incluirla los sobreescribirá.
     */
    protected function configure() {
        $this->setName('swd:actualizar_participantes')
                ->setDescription('Actualiza las noticias, trayendose todos las entradas de blogger desde ayer o desde la fecha deseada.')
                ->addArgument('fecha', InputArgument::OPTIONAL, '¿Desde que fecha?')
                ->addOption('mantenerAnteriores', null, InputOption::VALUE_NONE, 'Mantiene los entradas ya existentes');
    }

    /**
     * Función principal que pide las entradas a la API de Blogger y actualiza el archivo de
     * actividades de la web
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $queryP = "SELECT * FROM Participante";
        $participantes = $this->conn->executeQuery($queryP)->fetchAll();
        foreach ($participantes as $p) {
            $participante = new Participante();
            foreach ($p as $clave => $valor) {
                $metodo = 'set' . $clave;
                if (method_exists($participante, $metodo)) {
                    $participante->$metodo($valor);
                }
            }
            $participante->setComoConoce($p['como_conoce']);
            $participante->setParticipaSorteos($p['participa_sorteos']);
            $participante->setNumeroEntrada($p['numero_entrada']);
            $participante->addRole('ROLE_PARTICIPANTE');
            $participante->setUsername($participante->getEmail());
            $participante->setPassword('1q2w3e');
            $this->em->persist($participante);
            $this->em->flush();
            $this->userManipulator->activate($participante->getUsername());
            $this->userManipulator->changePassword($participante->getUsername(), $participante->getNumeroEntrada());
            $random = substr($participante->getEmail(), 1, 2);
            $ficheroTicket = $container->get('kernel')->getRootDir() . '/../web/tickets/' . $random . $participante->getNumeroEntrada() . '.pdf';
            var_dump($router->generate('verTicket', ['numeroEntrada' => $participante->getNumeroEntrada()], true));
            var_dump($ficheroTicket);
            if (!file_exists($ficheroTicket)) {
                //$container->get('knp_snappy.pdf')->generate($router->generate('verTicket', ['numeroEntrada' => $participante->getNumeroEntrada()], true), $ficheroTicket);
            }
            $output->writeln("<info>¡Participante " . $participante->getId() . " añadido!</info>");
        }
    }

}
