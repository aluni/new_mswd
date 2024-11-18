<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Form\ParticipanteFiltrosType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class AdminController extends AluniController {

    /**
     * @return Response
     * @Route("/lista-participantes", name="lista-participantes")
     * @Security("has_role('ROLE_INSTITUCION') || has_role('ROLE_EMPLEADO')")
     * @Template
     */
    public function listaParticipantesAction() {
        $paises = $this->get('database_connection')->executeQuery('SELECT es FROM Pais')->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($paises as $i => $pais) {
            $paises[$pais] = $pais;
            unset($paises[$i]);
        }
        $formFiltrosParticipante = $this->createForm(new ParticipanteFiltrosType($paises));
        $paises = $this->getDoctrine()->getRepository('SWDMadridBundle:Pais')->findAll();
        $formSorteo = $this->createForm(new SorteoType());
        $comoConoce = $this->getDoctrine()->getRepository('SWDMadridBundle:ComoConoce')->findBy([], ['comoConoce' => 'ASC']);
        return ['paises' => $paises,
            'comoConoce' => $comoConoce,
            'formFiltrosParticipante' => $formFiltrosParticipante->createView(),
            'formSorteo' => $formSorteo->createView(),
            'cc' => ''];
    }

    /**
     * @return Response
     * @Route("/sorteos", name="sorteos")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Template
     */
    public function sorteosAction() {
        $paises = $this->get('database_connection')->executeQuery('SELECT es FROM Pais')->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($paises as $i => $pais) {
            $paises[$pais] = $pais;
            unset($paises[$i]);
        }
        $participantesREST = $this->generateUrl('participantesREST');
        $formFiltrosParticipante = $this->createForm(new ParticipanteFiltrosType($paises));
        $formSorteo = $this->createForm(new SorteoType());
        return ['participantesREST' => $participantesREST,
            'formFiltrosParticipante' => $formFiltrosParticipante->createView(),
            'formSorteo' => $formSorteo->createView(),
            'sorteos' => true,
            'cc' => ''];
    }

    /**
     * @Route("/participantes/{id}", requirements={"_format"="json"})
     * @Method({"PUT"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateParticipanteAction(Request $request, $id) {
        $participante = $this->getDoctrine()->getRepository('SWDMadridBundle:Participante')->find($id);
        $datosParticipante = json_decode($request->getContent(), true);
        $participante->setAsistido($datosParticipante['asistido']);
        $participante->setSorteo($datosParticipante['sorteo']);
        $this->getDoctrine()->getManager()->flush();
        return $this->respuestaJSON(['mensaje' => 'Guardado correctamente'], 200);
    }

    /**
     * @param mixed $data
     * @param integer $status_code
     * @return Response
     */
    public function respuestaJSON($data, $status_code = 200, $ignoredAttr = []) {
        if (is_object($data) || is_array($data)) {
            $encoder = new JsonEncoder();
            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceHandler(function ($object) {
                return strval($object);
            });
            $normalizer->setIgnoredAttributes($ignoredAttr);
            $serializer = new Serializer(array($normalizer), array($encoder));
            $json = $serializer->serialize($data, 'json', ['groups' => ['listado']]);
        } else {
            $json = $data;
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json;charset=UTF-8');
        $response->setContent($json);
        $response->setStatusCode($status_code);
        return $response;
    }

}
