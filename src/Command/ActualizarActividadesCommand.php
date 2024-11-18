<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
class ActualizarActividadesCommand extends AluniCommand {

    /**
     * El comando recibe un argumento que es opcional(fecha), en caso de que este argumento no se
     * incluya se entenderá que se quieren pedir todas las entradas del último año. La opción de 
     * mantener anteriores mantiene los entrada ya existentes que hayamos importado en ocasiones
     * anteriores, en caso de no incluirla los sobreescribirá.
     */
    protected function configure() {
        $this->setName('swd:actualizar_noticias')
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
        $container = $this->getContainer();
        $fecha = $this->prepararFecha($input->getArgument('fecha'));
        $key = $container->getParameter('googleAPIkeyServer');
        $blogId = $container->getParameter('blogId');
        $url = "https://www.googleapis.com/blogger/v3/blogs/$blogId/posts?key=$key&status=live&startDate=$fecha&maxResults=50";
        $entradasBlogger = $this->getEntradas($url);
        $data = json_decode($entradasBlogger);
        if (isset($data->items)) {
            $entradas = $data->items;
            foreach ($entradas as &$entrada) {
                $this->prepararEntrada($entrada);
            }
            $ficheroEntradas = $container->getParameter('directorioEntradasBlogs') . '/mswd-noticias.json';
            $this->guardarEntradas($ficheroEntradas, $input->getOption('mantenerAnteriores'), $entradas);
            $output->writeln("<info>¡Entradas del blog desde $fecha importados correctamente!</info>");
        } else {
            $output->writeln("<comment>No hay nuevos entradas desde $fecha</comment>");
        }
    }

    /**
     * Formatea la fecha en caso de que se haya dado como argumento de entrada, o crea una fecha
     * nueva igual a la actual menos un año. Esta fecha será a partir de la cual se piden las
     * entradas a la API de Blogger.
     * 
     * @param string $fecha
     * @return string
     */
    private function prepararFecha($fecha) {
        if (empty($fecha)) {
            $dt = new \DateTime();
            $dt->setTimestamp(strtotime("today -1 year"));
            return $dt->format("Y-m-d\TH:i:s") . 'Z';
        } else {
            return $fecha . 'T00:00:00Z';
        }
    }

    /**
     * Una petición GET estándar a una url determinada mediante la librería CURL. Devuelve un string
     * con la respuesta.
     * 
     * @param string $url
     * @return string
     */
    private function getEntradas($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * Puesto que la información que devuelve Google a la petición a la API de Blogger es muy amplia
     * necesitamos filtrarla y quedarnos con las partes que nos interesan. Además necesitamos buscar
     * y extraer la primera imagen de la entrada para asi poder mostrarla en otros sitios, como en
     * el listado de actividades de cada ciudad.
     * 
     * @param object $entrada
     */
    private function prepararEntrada(&$entrada) {
        unset($entrada->kind);
        unset($entrada->blog);
        unset($entrada->author->id);
        unset($entrada->author->url);
        unset($entrada->author->image);
        unset($entrada->replies);
        $coincidencias = [];
        preg_match_all('/<img[^>]*?\s+src\s*=\s*"([^"]+)"[^>]*?>/i', $entrada->content, $coincidencias);
        if (!empty($coincidencias[1][0])) {
            $entrada->image = $coincidencias[1][0];
        } else {
            $entrada->image = 'http://www.aluni.net/uploads/buscador/aluni_logo_para_pc_gris.jpg';
        }
    }

    /**
     * Guarda las entradas de actividades de una ciudad en su correspondiente archivo. Recordar que 
     * este archivo será el que se lea a la hora de mostrar las actividades en la web.
     * 
     * @param string $fichero
     * @param boolean $mantenerAnteriores
     * @param array $entradas
     */
    private function guardarEntradas($fichero, $mantenerAnteriores, $entradas) {
        if ($mantenerAnteriores && file_exists($fichero)) {
            $readHandle = fopen($fichero, 'r') or die('No se ha podido abrir el fichero:  ' . $fichero); //open file for writing ('w','r','a')...
            $str = fread($readHandle, filesize($fichero));
            fclose($readHandle);
            $entradasAntiguos = json_decode($str);
            $entradas = array_merge($entradasAntiguos, $entradas);
        }
        $json = json_encode($entradas);
        $writeHandle = fopen($fichero, 'w') or die('No se ha podido abrir el fichero:  ' . $fichero); //open file for writing ('w','r','a')...
        fwrite($writeHandle, $json);
        fclose($writeHandle);
    }

}
