<?php

//

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Actualiza los entrada de actividades desde Blogger. Este comando hace una petición HTTP a la API
 * de Blogger de Google para pedir un listado de entradas en formato json desde la fecha que
 * indiquemos. Estas entradas de actividades se guardarán en una carpeta del proyecto definida por
 * configuración, y serán las que se muestren en la web.
 *
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class ActualizarInstitucionesCommand extends AluniCommand {

    /**
     * El comando recibe un argumento que es opcional(fecha), en caso de que este argumento no se
     * incluya se entenderá que se quieren pedir todas las entradas del último año. La opción de
     * mantener anteriores mantiene los entrada ya existentes que hayamos importado en ocasiones
     * anteriores, en caso de no incluirla los sobreescribirá.
     */
    protected function configure(): void {
        $this->setName('swd:actualizar_instituciones')
                ->setDescription('Actualiza las noticias, trayendose todos las entradas de blogger desde ayer o desde la fecha deseada.')
                ->addArgument('id', InputArgument::OPTIONAL, '¿Id de la institucion?')
                ->addOption('mantenerAnteriores', null, InputOption::VALUE_NONE, 'Mantiene los entradas ya existentes');
    }

    /**
     * Función principal que pide las entradas a la API de Blogger y actualiza el archivo de
     * actividades de la web
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $instituciones = $this->em->getRepository('SWDMadridBundle:Institucion')->findBy(['enabled' => 1]);
        foreach ($instituciones as $institucion) {
            $alias = $institucion->getAlias();
            $pass = 'mswd' . substr($alias, -2) . substr($alias, 0, 2);
            $this->userManipulator->changePassword($institucion->getUsername(), $pass);
            $output->writeln("<info>" . $institucion->getNombre() . " - Usuario: $alias - Contraseña: $pass</info>");
        }
        return Command::SUCCESS;
    }

}
