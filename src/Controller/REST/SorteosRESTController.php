<?php

namespace App\Controller\REST;

use App\Entity\Sorteo;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador encargado del servicio REST de sorteos de s. Usa funcionalidades del FOSRestBundle (creación de 
 * rutas y de respuestas en formato json), el cual usa  a su vez el JMSSerializationBundle (encargados de la 
 * serialización de entidades).
 * 
 * @author Álvaro Peláez Santana
 * @copyright ALUNI MADRID S.L.
 */
class SorteosRESTController extends AluniRESTController {

    /**
     * @Rest\View(serializerGroups={"lista-sorteos"})
     * @Rest\Get("/sorteos", name="get_sorteos")
     * @Security("is_granted('ROLE_INSTITUCION') || is_granted('ROLE_EMPLEADO')")
     * @return array|object[]|Sorteo[]
     */
    public function getSorteosAction(): array {
        if ($this->seguridad->esInstitucion()) {
            $checkeos = $this->getUser()->getCheckeos();
            $sorteos = [];
            foreach ($checkeos as $checkeo) {
                $sorteos[] = $checkeo->getSorteo();
            }
        } else {
            $sorteos = $this->doctrine->getRepository(Sorteo::class)->findAll();
        }
        return $sorteos;
    }

    /**
     * Actualiza un recibo con sus desgloses a partir de la información que llega por la Request en formato json.
     *
     *
     * @Rest\Post("/sorteos")
     * @param Request $request
     * @return JsonResponse
     */
    public function postSorteoAction(Request $request): JsonResponse {
        $sorteo = $this->serializer->deserialize($request->getContent(), 'App\Entity\Sorteo', 'json');
        $this->em->persist($sorteo);
        foreach ($sorteo->getParticipantes() as $participante) {
            $participante->addSorteo($sorteo);
            $this->em->persist($participante);
        }
        $this->em->flush();
        return new JsonResponse(['mensaje' => "Sorteo creado correctamente"]);
    }

    /**
     * Actualiza un recibo con sus desgloses a partir de la información que llega por la Request en formato json.
     *
     * @Rest\Put("/sorteos/{id}")
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function putSorteoAction(Request $request, int $id): JsonResponse {
        $sorteo = $this->serializer->deserialize(
                $request->getContent(), 'App\Entity\Sorteo', 'json');
        
        $this->em->persist($sorteo);
        $this->em->flush();
        return new JsonResponse(['mensaje' => "Sorteo $id actualizado correctamente"]);
    }

}
