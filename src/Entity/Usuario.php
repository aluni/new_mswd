<?php
// src/AppBundle/Entity/User.php

namespace SWD\MadridBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="tipo", type="string")
 * @ORM\DiscriminatorMap({"usuario" = "Usuario", "participante" = "Participante", "institucion" = "Institucion"})
 * @Serializer\Discriminator(field = "tipo", disabled = false, map = {
 *      "usuario": "SWD\MadridBundle\Entity\Usuario", 
 *      "participante": "SWD\MadridBundle\Entity\Participante", 
 *      "institucion": "SWD\MadridBundle\Entity\Institucion"
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
