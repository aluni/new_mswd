<?php

namespace SWD\MadridBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Checkeo
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Checkeo {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"lista-participantes"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity="Participante", inversedBy="checkeos", cascade={"persist", "remove"}) 
     */
    private $participante;

    /**
     * @ORM\ManyToOne(targetEntity="Institucion", inversedBy="checkeos", cascade={"persist", "remove"}) 
     * @Serializer\Groups({"lista-participantes"})
     * @Serializer\Type("SWD\MadridBundle\Entity\Institucion")
     */
    private $institucion;

    /** @ORM\PrePersist */
    public function fechaOnPrePersist(){
        $this->fecha = date_create();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Checkeo
     */
    public function setFecha($fecha) {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha() {
        return $this->fecha;
    }

    /**
     * Set participante
     *
     * @param \SWD\MadridBundle\Entity\Participante $participante
     *
     * @return Checkeo
     */
    public function setParticipante(\SWD\MadridBundle\Entity\Participante $participante = null) {
        $this->participante = $participante;

        return $this;
    }

    /**
     * Get participante
     *
     * @return \SWD\MadridBundle\Entity\Participante
     */
    public function getParticipante() {
        return $this->participante;
    }

    /**
     * Set institucion
     *
     * @param \SWD\MadridBundle\Entity\Institucion $institucion
     *
     * @return Checkeo
     */
    public function setInstitucion(\SWD\MadridBundle\Entity\Institucion $institucion = null) {
        $this->institucion = $institucion;

        return $this;
    }

    /**
     * Get institucion
     *
     * @return \SWD\MadridBundle\Entity\Institucion
     */
    public function getInstitucion() {
        return $this->institucion;
    }

    public function __toString() {
        return $this->institucion->getNombre();
    }

}
