<?php

namespace App\Controller\REST;

use App\Entity\Participante;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador encargado del servicio REST de participantes de s. Usa funcionalidades del FOSRestBundle (creación de 
 * rutas y de respuestas en formato json), el cual usa  a su vez el JMSSerializationBundle (encargados de la 
 * serialización de entidades).
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class ParticipantesRESTController extends AluniRESTController {

    /**
     * @Rest\View(serializerGroups={"lista-participantes"})
     * @Rest\Get("/participantes", name="get_participantes")
     * @Security("has_role('ROLE_INSTITUCION') || has_role('ROLE_EMPLEADO')")
     * @param Request $request
     * @return array|object[]|Participante[]
     */
    public function getParticipantesAction(Request $request): array {
        if ($this->seguridad->esInstitucion()) {
            $checkeos = $this->getUser()->getCheckeos();
            $participantes = [];
            foreach ($checkeos as $checkeo) {
                $participantes[] = $checkeo->getParticipante();
            }
        } else {
            $participantes = $this->doctrine->getRepository(Participante::class)->findAll();
        }
        return $participantes;
    }

    /**
     * Devuelve una participante determinada en formato json.
     *
     * @Rest\View(serializerGroups={"lista-participantes"})
     * @Rest\Get("/participantes/{id}")
     * @param integer $id
     * @return Participante
     */
    public function getParticipanteAction(int $id): Participante {
        return $this->doctrine->getRepository(Participante::class)->find($id);
    }

    /**
     * Actualiza un recibo con sus desgloses a partir de la información que llega por la Request en formato json.
     *
     *
     * @Rest\Put("/participantes/{id}")
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function putParticipanteAction(Request $request, int $id): JsonResponse {
        $participante = $this->serializer->deserialize(
                $request->getContent(), 
                'App\Entity\Participante', 
                'json');
        
        $this->em->persist($participante);
        $this->em->flush();
        return new JsonResponse(['mensaje' => "Participante $id actualizado correctamente"]);
    }

}
