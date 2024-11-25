<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pais
 *
 * @ORM\Table(name="Pais")
 * @ORM\Entity
 */
class Pais {

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
     * @ORM\Column(name="iso", type="string", length=45)
     */
    private $iso;
    
    /**
     * @var string
     *
     * @ORM\Column(name="es", type="string", length=45)
     */
    private $es;
    
    /**
     * @var string
     *
     * @ORM\Column(name="en", type="string", length=45)
     */
    private $en;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fr", type="string", length=45)
     */
    private $fr;
    
    /**
     * @var string
     *
     * @ORM\Column(name="de", type="string", length=45)
     */
    private $de;
    
    /**
     * @var string
     *
     * @ORM\Column(name="zh", type="string", length=45)
     */
    private $zh;
    
    /**
     * @var string
     *
     * @ORM\Column(name="it", type="string", length=45)
     */
    private $it;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ar", type="string", length=45)
     */
    private $ar;
    
    /**
     * @var string
     *
     * @ORM\Column(name="pl", type="string", length=45)
     */
    private $pl;
    
    /**
     * @var string
     *
     * @ORM\Column(name="pt", type="string", length=45)
     */
    private $pt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ru", type="string", length=45)
     */
    private $ru;
    
    public function getId() {
        return $this->id;
    }

    public function getIso() {
        return $this->iso;
    }

    public function getEs() {
        return $this->es;
    }

    public function getEn() {
        return $this->en;
    }

    public function getFr() {
        return $this->fr;
    }

    public function getDe() {
        return $this->de;
    }

    public function getZh() {
        return $this->zh;
    }

    public function getIt() {
        return $this->it;
    }

    public function getAr() {
        return $this->ar;
    }

    public function getPl() {
        return $this->pl;
    }

    public function getPt() {
        return $this->pt;
    }

    public function getRu() {
        return $this->ru;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setIso($iso) {
        $this->iso = $iso;
    }

    public function setEs($es) {
        $this->es = $es;
    }

    public function setEn($en) {
        $this->en = $en;
    }

    public function setFr($fr) {
        $this->fr = $fr;
    }

    public function setDe($de) {
        $this->de = $de;
    }

    public function setZh($zh) {
        $this->zh = $zh;
    }

    public function setIt($it) {
        $this->it = $it;
    }

    public function setAr($ar) {
        $this->ar = $ar;
    }

    public function setPl($pl) {
        $this->pl = $pl;
    }

    public function setPt($pt) {
        $this->pt = $pt;
    }

    public function setRu($ru) {
        $this->ru = $ru;
    }

    public function __toString() {
        return $this->es;
    }

}
