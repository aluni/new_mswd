<?php

namespace App\Controller;


use App\Entity\ComoConoce;
use App\Entity\Pais;
use App\Entity\Sorteo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends AluniController {

    /**
     * @Route("/students", name="home")
     * @Template
     */
    public function indexAction(Request $request): array {
        $cc = $request->query->get('cc');
        $paises = $this->doctrine->getRepository(Pais::class)->findAll();
        $comoConoce = $this->doctrine->getRepository(ComoConoce::class)->findBy([], ['comoConoce' => 'ASC']);
        return ['paises' => $paises, 'comoConoce' => $comoConoce, 'cc' => $cc];
    }

    /**
     * @Route("/clausula-privacidad", name="clausulaPrivacidad")
     * @Template
     */
    public function clausulaPrivacidadAction(): array {
        return [];
    }

    /**
     * @Route("/aviso-legal", name="avisoLegal")
     * @Template
     */
    public function avisoLegalAction(): array {
        return [];
    }

    /**
     * @Route("/politica-cookies", name="politicaCookies")
     * @Template
     */
    public function politicaCookiesAction(): array {
        return [];
    }

    /**
     * @Route("/concurso", name="concurso")
     * @Template
     */
    public function concursoAction(): array {
        return [];
    }

    /**
     * @Route("/enviarEmailContactar", name="enviarEmailContactar", methods={"POST"})
     */
    public function enviarEmailContactarAction(Request $request): Response {
        $email = $request->request->get('email');
        $nombre = $request->request->get('nombre');
        $mensaje = "<p>Nombre: <b>$nombre</b></p>"
                . "<p>Email: <b>$email</b></p>"
                . "<p>Mensaje: <b>" . $request->request->get('mensaje') . "</b></p>";
        $this->enviarEmail($mensaje, "Mensaje desde contactar MSWD - $nombre", 'diego.poole@gmail.com');
        return new Response('Email enviado correctamente');
    }

    /**
     * @Route("/lista-ganadores", name="lista_ganadores")
     * @Template
     */
    public function listaGanadoresAction(): array {
        $sorteos = $this->doctrine->getRepository(Sorteo::class)->findAll();
        return ['sorteos' => $sorteos];
    }

    /**
     * @Route("/boletin-universia", name="boletin_universia")
     * @Template("mails/boletinUniversia.html.twig")
     */
    public function boletinUniversiaAction(): array {
        return [];
    }


}
