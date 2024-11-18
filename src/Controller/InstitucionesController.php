<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use App\Entity\Checkeo;

/**
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class InstitucionesController extends AluniController {

    /**
     * @Route("/", name="instituciones")
     * @Template()
     */
    public function institucionesAction() {
        return [];
    }

    /**
     * @Route("/lista-instituciones", name="lista_instituciones")
     * @Template()
     */
    public function listaInstitucionesAction() {
        $institucionesREST = $this->generateUrl('institucionesREST');
        return ['institucionesREST' => $institucionesREST];
    }

    /**
     * @Route("/ver-institucion/{alias}", name="ver_institucion", defaults={"alias"=""})
     * @Template()
     */
    public function verInstitucionAction($alias) {
        $institucion = $this->getDoctrine()->getRepository('SWDMadridBundle:Institucion')->findOneByAlias($alias);
        $descripcion = $this->normalizar($institucion->getDescripcion());
        $descripcion = preg_replace('/\n/', '</p><p>', $descripcion);
        $descripcion = '<p>' . $descripcion . '</p>';
        $descripcion = preg_replace('!(http|ftp|scp)(s)?:\/\/[a-zA-Z0-9\-.?&_/]+!', "<a href=\"\\0\">\\0</a>", $descripcion);
        $institucion->setDescripcion($descripcion);
        return ['institucion' => $institucion];
    }

    /**
     * @Route("/convenios", requirements={"_format"="json"}, name="institucionesREST")
     * @Method({"GET"})
     */
    public function readInstitucionesCollectionAction() {
        $queryInstituciones = "SELECT i.* FROM Institucion i "
                . "INNER JOIN Usuario u ON u.id = i.id "
                . "WHERE u.enabled=1 ORDER BY i.rango ASC, i.id ASC";
        $instituciones = $this->get('database_connection')->executeQuery($queryInstituciones)->fetchAll();
        return $this->respuestaJSON($instituciones, 200);
    }

    /**
     * @Route("/comprobar-participante/{id}", name="comprobarParticipante",  defaults={"id"=""})
     */
    public function comprobarParticipanteAction($id) {
        $em = $this->getDoctrine()->getManager();
        if ($this->get('seg_service')->esInstitucion()) {
            $participante = $em->getRepository('SWDMadridBundle:Participante')->find($id);
            $this->addFlash('success', $this->getUser()->getNombre());
            if (!empty($participante)) {
                $checkeo = $em->getRepository('SWDMadridBundle:Checkeo')->findOneBy(
                        ['participante' => $participante, 'institucion' => $this->getUser()]);
                if (empty($checkeo)) {
                    $checkeo = new Checkeo();
                    $checkeo->setInstitucion($this->getUser());
                    $checkeo->setParticipante($participante);
                    $em->persist($checkeo);
                    $em->flush();
                    $this->addFlash('success', "Participante checkeado!!.");
                } else {
                    $this->addFlash('error', "Participante no válido, ya ha sido checkeado!!.");
                }
            } else {
                $this->addFlash('error', "Participante no válido!!.");
            }
            return $this->redirect($this->generateUrl('lista-participantes'));
        } else {
            $this->addFlash('notice', "¡Para empezar a checkear participantes logeate como institución!");
            return $this->redirect($this->generateUrl('home'));
        }
    }

    /**
     * @Security("has_role('ROLE_INSTITUCION')")
     * @Route("/checkear-participante/{email}", name="checkearParticipante",  defaults={"email"=""})
     */
    public function checkearParticipanteAction($email) {
        $em = $this->getDoctrine()->getManager();
        $participante = $em->getRepository('SWDMadridBundle:Participante')->findOneByEmail($email);
        if (!empty($participante)) {
            $checkeo = $em->getRepository('SWDMadridBundle:Checkeo')->findOneBy(
                    ['participante' => $participante, 'institucion' => $this->getUser()]);
            if (empty($checkeo)) {
                $checkeo = new Checkeo();
                $checkeo->setInstitucion($this->getUser());
                $checkeo->setParticipante($participante);
                $em->persist($checkeo);
                $em->flush();
                return $this->respuestaJSON(["Participante checkeado!!."], 200);
            } else {
                return $this->respuestaJSON(["Participante no válido, ya ha sido checkeado!!."], 422);
            }
        } else {
            return $this->respuestaJSON(["Participante no válido!!!"], 422);
        }
    }

    /**
     * @Security("has_role('ROLE_INSTITUCION')")
     * @Route("/descargar-checkeados", name="descargarCheckeados")
     */
    public function descargarCheckeadosAction() {
        $em = $this->getDoctrine()->getManager();
        $checkeos = $this->getUser()->getCheckeos();
        $participantes = [];
        $csvstring = ('"Nº entrada";"Nombre completo";"Email";"Nacionalidad";"Sexo";"Universidad";"¿Cómo ha conocido el evento?"' . "\n");
        foreach ($checkeos as $checkeo) {
            $participante = $checkeo->getParticipante();
            $csvstring .= $this->encodeCSVField(substr('0000' . $participante->getNumeroEntrada(), -5)) . ";"
                    . $this->encodeCSVField($participante->getNombre() . " " . $participante->getApellidos()) . ";"
                    . $this->encodeCSVField($participante->getEmail()) . ";"
                    . $this->encodeCSVField($participante->getNacionalidad()) . ";"
                    . $this->encodeCSVField(($participante->getSexo() == 1 ? 'Hombre' : 'Mujer')) . ";"
                    . $this->encodeCSVField($participante->getUniversidad()) . ";"
                    . $this->encodeCSVField($participante->getComoConoce()) . "\n";
        }
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=Lista checkeados " . $this->getUser()->getNombre() . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $csvstring;
        die;
    }

    /**
     * Crea una respuesta en formato JSON a partir de un array o un objeto.
     *
     * @param mixed $data
     * @param integer $status_code
     * @return Response
     */
    public function respuestaJSON($data, $status_code) {
        if (is_object($data) || is_array($data)) {
            $encoders = [new JsonEncoder()];
            $normalizers = [new GetSetMethodNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            $json = $serializer->serialize($data, 'json');
        } else {
            $json = $data;
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json;charset=UTF-8');
        $response->setContent($json);
        $response->setStatusCode($status_code);
        return $response;
    }

    public function encodeCSVField($string) {
        if (strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
            $string = '"' . str_replace('"', '""', $string) . '"';
        }
        return $string;
    }

    private function normalizar($str) {
        // Normalize line endings
        // Convert all line-endings to UNIX format
        $s = str_replace("\r\n", "\n", $str);
        $s = str_replace("\r", "\n", $s);
        // Don't allow out-of-control blank lines
        $s = preg_replace("/\n{2,}/", "\n", $s);
        return $s;
    }

}
