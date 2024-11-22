<?php

namespace App\Controller;


use App\Entity\Institucion;
use App\Entity\Participante;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
    public function institucionesAction(): array {
        return [];
    }

    /**
     * @Route("/lista-instituciones", name="lista_instituciones")
     * @Template()
     */
    public function listaInstitucionesAction(): array {
        $institucionesREST = $this->generateUrl('institucionesREST');
        return ['institucionesREST' => $institucionesREST];
    }

    /**
     * @Route("/ver-institucion/{alias}", name="ver_institucion", defaults={"alias"=""})
     * @Template()
     */
    public function verInstitucionAction($alias): array {
        $institucion = $this->doctrine->getRepository(Institucion::class)->findOneBy(['alias' => $alias]);
        $descripcion = $this->normalizar($institucion->getDescripcion());
        $descripcion = preg_replace('/\n/', '</p><p>', $descripcion);
        $descripcion = '<p>' . $descripcion . '</p>';
        $descripcion = preg_replace('!(http|ftp|scp)(s)?://[a-zA-Z0-9\-.?&_/]+!', '<a href="$1">$1</a>', $descripcion);
        $institucion->setDescripcion($descripcion);
        return ['institucion' => $institucion];
    }
    /**
     * @Route("/convenios", requirements={"_format"="json"}, name="institucionesREST", methods={"GET"})
     */
    public function readInstitucionesCollectionAction(): Response {
        $queryInstituciones = "SELECT i.* FROM Institucion i "
                . "INNER JOIN Usuario u ON u.id = i.id "
                . "WHERE u.enabled=1 ORDER BY i.rango, i.id";
        $instituciones = $this->conn->executeQuery($queryInstituciones)->fetchAll();
        return $this->respuestaJSON($instituciones, 200);
    }

    /**
     * @Route("/comprobar-participante/{id}", name="comprobarParticipante",  defaults={"id"=""})
     */
    public function comprobarParticipanteAction($id): RedirectResponse {
        
        if ($this->seguridad->esInstitucion()) {
            $participante = $this->em->getRepository(Participante::class)->find($id);
            $this->addFlash('success', $this->getUser()->getNombre());
            if (!empty($participante)) {
                $checkeo = $this->em->getRepository(Checkeo::class)->findOneBy(
                        ['participante' => $participante, 'institucion' => $this->getUser()]);
                if (empty($checkeo)) {
                    $checkeo = new Checkeo();
                    $checkeo->setInstitucion($this->getUser());
                    $checkeo->setParticipante($participante);
                    $this->em->persist($checkeo);
                    $this->em->flush();
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
    public function checkearParticipanteAction($email): Response {
        
        $participante = $this->em->getRepository(Participante::class)->findOneByEmail($email);
        if (!empty($participante)) {
            $checkeo = $this->em->getRepository(Checkeo::class)->findOneBy(
                    ['participante' => $participante, 'institucion' => $this->getUser()]);
            if (empty($checkeo)) {
                $checkeo = new Checkeo();
                $checkeo->setInstitucion($this->getUser());
                $checkeo->setParticipante($participante);
                $this->em->persist($checkeo);
                $this->em->flush();
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
    #[NoReturn] public function descargarCheckeadosAction(): void {
        
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
    public function respuestaJSON(mixed $data, int $status_code): Response {
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
        if (str_contains($string, ',') || str_contains($string, '"') || str_contains($string, "\n")) {
            $string = '"' . str_replace('"', '""', $string) . '"';
        }
        return $string;
    }

    private function normalizar($str): array|string|null {
        // Normalize line endings
        // Convert all line-endings to UNIX format
        $s = str_replace("\r\n", "\n", $str);
        $s = str_replace("\r", "\n", $s);
        // Don't allow out-of-control blank lines
        return preg_replace("/\n{2,}/", "\n", $s);
    }

}
