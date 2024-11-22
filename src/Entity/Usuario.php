<?php
// src/AppBundle/Entity/User.php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="tipo", type="string")
 * @ORM\DiscriminatorMap({"usuario" = "Usuario", "participante" = "Participante", "institucion" = "Institucion"})
 * @Serializer\Discriminator(field = "tipo", disabled = false, map = {
 *      "usuario": "App\Entity\Usuario", 
 *      "participante": "App\Entity\Participante", 
 *      "institucion": "App\Entity\Institucion"
 * })
 */
class Usuario extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"lista-participantes"})
     */
    protected $id;
    
    /**
     * @Serializer\Groups({"lista-participantes"})
     */
    protected $email;

    public function __construct()
    {
        parent::__construct();
    }
}
