<?php

namespace PHPM\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use PHPM\Bundle\Validator\DebutAvantFin;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use PHPM\Bundle\Validator\QuartHeure;
use PHPM\Bundle\Validator\Recoupe;

/**
 * PHPM\Bundle\Entity\BesoinOrga
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PHPM\Bundle\Entity\BesoinOrgaRepository")
 * 
 * 
 */
class BesoinOrga
{
	
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
    * @ORM\ManyToOne(targetEntity="PlageHoraire")
    * @ORM\JoinColumn(name="plageHoraire_id", referencedColumnName="id",onDelete="CASCADE")
    * @Assert\Valid
    */
    protected $plageHoraire;
    
    /**
     * @ORM\ManyToOne(targetEntity="Equipe")
     * @ORM\JoinColumn(name="equipe_id", referencedColumnName="id",onDelete="SET NULL")
     * @Assert\Valid
     */
    protected $equipe;
    
    /**
    * @var smallint $nbOrgasNecessaires
    *
    * @ORM\Column(name="nbOrgasNecessaires", type="smallint", nullable=true)
    */
    protected $nbOrgasNecessaires;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Orga", inversedBy="besoinsOrgaHint")
     * @ORM\JoinColumn(name="orgaHint_id", referencedColumnName="id",onDelete="SET NULL")
     * @Assert\Valid
     */
    protected $orgaHint;
    
   

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
     * Set nbOrgasNecessaires
     *
     * @param smallint $nbOrgasNecessaires
     */
    public function setNbOrgasNecessaires($nbOrgasNecessaires)
    {
        $this->nbOrgasNecessaires = $nbOrgasNecessaires;
    }

    /**
     * Get nbOrgasNecessaires
     *
     * @return smallint 
     */
    public function getNbOrgasNecessaires()
    {
        return $this->nbOrgasNecessaires;
    }

    /**
     * Set plageHoraire
     *
     * @param PHPM\Bundle\Entity\PlageHoraire $plageHoraire
     */
    public function setPlageHoraire(\PHPM\Bundle\Entity\PlageHoraire $plageHoraire)
    {
        $this->plageHoraire = $plageHoraire;
    }

    /**
     * Get plageHoraire
     *
     * @return PHPM\Bundle\Entity\PlageHoraire 
     */
    public function getPlageHoraire()
    {
        return $this->plageHoraire;
    }

    /**
     * Set equipe
     *
     * @param PHPM\Bundle\Entity\Equipe $equipe
     */
    public function setEquipe($equipe)
    {
        $this->equipe = $equipe;
    }

    /**
     * Get equipe
     *
     * @return PHPM\Bundle\Entity\Equipe 
     */
    public function getEquipe()
    {
        return $this->equipe;
    }


    /**
     * Set orgaHint
     *
     * @param PHPM\Bundle\Entity\Orga $orgaHint
     */
    public function setOrgaHint($orgaHint)
    {
        $this->orgaHint = $orgaHint;
    }

    /**
     * Get orgaHint
     *
     * @return PHPM\Bundle\Entity\Orga 
     */
    public function getOrgaHint()
    {
        return $this->orgaHint;
    }

    
}