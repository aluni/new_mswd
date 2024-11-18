<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ComoConoce
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ComoConoce
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="como_conoce", type="string", length=100)
     */
    private $comoConoce;
    
    /**
     * @var string
     *
     * @ORM\Column(name="etiqueta", type="string", length=50)
     */
    private $etiqueta;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comoConoce
     *
     * @param string $comoConoce
     *
     * @return Participante
     */
    public function setComoConoce($comoConoce)
    {
        $this->comoConoce = $comoConoce;

        return $this;
    }

    /**
     * Get comoConoce
     *
     * @return string
     */
    public function getComoConoce()
    {
        return $this->comoConoce;
    }
    
    
    public function __toString() {
        return $this->comoConoce;
    }
    
    /**
     * Get etiqueta
     *
     * @return string
     */
    function getEtiqueta() {
        return $this->etiqueta;
    }

    /**
     * Set etiqueta 
     *
     * @param string etiqueta
     *
     * @return Participante
     */
    function setEtiqueta($etiqueta) {
        $this->etiqueta = $etiqueta;

        return $this;
    }


}
