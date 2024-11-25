<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
/**
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class BlogController extends AluniController {

    /**
     * @return array
     * @Route("/lista-noticias", name="blog_listado_entradas")
     * @Template
     */
    public function listadoEntradasAction(): array {
        $urlEntradas = $this->generateUrl('blog_json_entradas');
        return ['urlEntradas' => $urlEntradas];
    }
    
    /**
     * @return Response
     * @Route("/json-noticias", name="blog_json_entradas")
     */
    public function jsonEntradas(): Response {
        return new Response(file_get_contents($this->getParameter('urlEntradasBlogs') . '/mswd-noticias.json'));
    }

    /**
     * @param integer $entradaId
     * @return array|void
     * @Route("/ver-noticia/{entradaId}", name="blog_ver_entrada", defaults={"entradaId" = ""})
     * @Template
     */
    public function verEntradaAction(int $entradaId) {
        $ficheroEntradas = $this->getParameter('directorioEntradasBlogs') . '/mswd-noticias.json';
        $handle = fopen($ficheroEntradas, 'r') or die('No se ha podido abrir el fichero:  ' . $ficheroEntradas);
        $str = fread($handle, filesize($ficheroEntradas));
        fclose($handle);
        $entradas = json_decode($str);
        foreach ($entradas as $entrada) {
            if ($entrada->id === $entradaId) {
                return ['entrada' => $entrada];
            }
        }
    }
}