<?php

namespace PHPM\Bundle\Entity;

use Doctrine\ORM\EntityRepository;
use PHPM\Bundle\Entity\Orga;
use PHPM\Bundle\Entity\Config;

/**
 * OrgaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrgaRepository extends EntityRepository
{
	public function getOrgasWithCriteria($permis, $maxDateNaissance, $plage_id, $niveau_confiance,$creneau, $bloc)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();
		$expr = $qb->expr();
	
		$andx = $expr->andx(
		$expr->eq('o.statut','1'),
		$expr->eq('d.orga','o'),
		$expr->neq('d.orga','0')		
		);
	
		$offset = (int)($bloc*50);
		$limit = (int)(50);
	
		if($plage_id !='')
		{
			$pref = json_decode($this->getEntityManager()->getRepository('PHPMBundle:Config')->findOneByField('manifestation.plages')->getValue(),TRUE);
			$plage= $pref[$plage_id];
			$andx->add('(d.debut < \''.$plage["fin"].'\' ) AND (d.fin >\''.$plage["debut"].'\' )');
		}
		if($permis!='')
		{
			$andx->add($expr->gte('o.permis',$permis));
		}
		if($maxDateNaissance !='')
		{
			$andx->add($expr->lte('o.dateDeNaissance','\''.$maxDateNaissance.'\''));
		}
		if($niveau_confiance !='')
		{
			$andx->add($expr->gte('o.confiance',$niveau_confiance));
		}
		if($creneau !='')
		{
			/*
			 * 
			//TODO
			//id_creneau : cet orga doit pouvoir etre affecté a ce créneau (dans les disponibilités et pas de créneau déja affecté)
			
			//créneau est disponible
			$entity = $em->getRepository('PHPMBundle:Creneau')->find($creneau);
			$andx->add($expr->eq($creneau->getDisponibilite(), 0));
			
			//on cherche les dispo de l'orga
			$dispo = $em->getRepository('PHPMBundle:Disponibilité')->find('*')->where('orga_id = o');
			//on vérifie que le créneau rentre dans les dispo et n'intérfère pas avec les autres créneaux. 
			
			*/
			/*
			$entity->getDebut() > $dispo->getDebut()
			$entity->getFin() < $dispo->getFin()
					$qb
					->select('o')
					->from('PHPMBundle:Orga','o')
					->from('PHPMBundle:Disponibilite', 'd')
					->from('PHPMBundle:Creneau', 'c')
					->where('c.id = \''.$creneau.'\' AND d.orga = o AND d.debut < c.debut AND d.fin > c.fin')
					
				
			*/
			
		}
		
		//$andx()->add('LIMIT\''.$limit.'\' \''.$offset.'\'');
		 
		$qb
		->select('o')
	
		->from('PHPMBundle:Orga','o')
	
	
	
		->from('PHPMBundle:Disponibilite', 'd')
		->from('PHPMBundle:Creneau', 'c')
		->from('PHPMBundle:PlageHoraire', 'p')
		->from('PHPMBundle:Tache', 't')

	
	
		->where($andx)
		//->setFirstResult($offset)
		//->setMaxResults($limit);
		
		;
		exit(var_dump($qb->getQuery()->getDQL()));
		
	
		return $qb->getQuery()->getResult();
	
	
	}
	
	public function getOrgasToValidate()
	{
	
		return $this->getEntityManager()
		->createQuery("SELECT o ,SUM(d.fin-d.debut)/3600 AS nbHeures FROM PHPMBundle:Orga o, PHPMBundle:Disponibilite d WHERE (d.orga = o AND o.statut=0)")
		
		->getResult();	
	
	
	}
	
        public function getOrgasFromRegistration()
    {
    
        return $this->getEntityManager()
        ->createQuery("SELECT o FROM PHPMBundle:Orga o WHERE (o.statut=0)")
        
        ->getResult();  
    
    
    }
    
            public function getNombreOrgas() 
    {
        return $this->getEntityManager()
        ->createQuery("SELECT COUNT (o.id) AS nbOrgas FROM PHPMBundle:Orga o WHERE (o.statut=1)")   
        ->getResult();
    }
    
            public function getNombreHeureDesCreneauNonAffecte() 
    {
        return $this->getEntityManager()
        ->createQuery("SELECT SUM(c.fin-c.debut)/3600 AS nbHeures FROM PHPMBundle:Creneau c  WHERE (c.disponibilite is NULL)")   
        ->getResult();
    }
    
    public function getNombreHeureInscription($oid)
    {
        return number_format($this->getEntityManager()
        ->createQuery("SELECT SUM(d.fin-d.debut)/3600 AS nbHeures FROM PHPMBundle:Orga o JOIN o.disponibilitesInscription d")
        ->getSingleScalarResult(),1);
    }
   
   
    /*    Voir comment on peut récupérer le résultat d'une requête SQL en natif
           public function getTacheSansCreneau() 
    {
        
        /*
        return $this->getEntityManager()
        ->createQuery("SELECT t FROM PHPMBundle:Tache t WHERE (t.id = (SELECT p.tache FROM PHPMBundle:PlageHoraire WHERE (p.id NOT IN (SELECT c.plageHoraire FROM PHPMBundle:Creneau))")   
        ->getResult();
    
         
         
         $conn = $em->getConnection();
         
         $sql = "SELECT t FROM PHPMBundle:Tache t WHERE (t.id = (SELECT p.tache FROM PHPMBundle:PlageHoraire WHERE (p.id NOT IN (SELECT c.plageHoraire FROM PHPMBundle:Creneau))";
         
         $conn->query($sql);

         $conn->close();
    }
    
    */
    
	public function search($s)
	{
		return $this->getEntityManager()
		->createQuery("SELECT o FROM PHPMBundle:Orga o WHERE (o.nom LIKE :s OR o.prenom LIKE :s OR o.surnom LIKE :s OR o.telephone LIKE :s OR o.email LIKE :s OR o.commentaire LIKE :s)")
		->setParameter('s', "%".$s."%")
		->getResult();	
	}
	
//	getOrgasWithCriteriaTache numéro 2 pour gérer le tache id
/*
public function getOrgasWithCompatibleTache($tache_id)
	{
		
		$qb = $this->getEntityManager()->createQueryBuilder();
		$expr = $qb->expr();
	
		$andx = $expr->andx(
		
		
		
		//$expr->eq('t.id',$tache_id),
		$expr->neq('t.id',$tache_id),
		
		$expr->eq('d.orga','o'),
		$expr->neq('d.orga','0'),
		$expr->eq('co.disponibilite','d'),
		$expr->eq('ct.plageHoraire','p'),
		$expr->eq('p.tache','t')
		
		
		$expr->eq('ct.disponibilite','0'),
		$expr->lte('ct.debut','d.fin'),
		$expr->gte('ct.fin','d.debut'),
		
				'ct.id NOT IN (SELECT ci.id FROM PHPMBundle:Creneau ci 
				WHERE 
				
				( (ci.debut < co.fin) AND (ci.fin > co.debut ) )
				OR
				(((ci.debut<p.debut)OR(ci.fin > p.fin))OR((ci.debut >= p.fin)OR(ci.fin <= p.debut)))
				
				)'
		
		
		);
		
		$qb
		->select('o,ct')
		
		->from('PHPMBundle:Orga','o')
		
		
		
		->from('PHPMBundle:Disponibilite', 'd')
		->from('PHPMBundle:Creneau', 'co')
		->from('PHPMBundle:PlageHoraire', 'p')
		->from('PHPMBundle:Tache', 't')
		->from('PHPMBundle:Creneau', 'ct')
		
		
		->where($andx);
		
		//exit(var_dump($qb->getQuery()->getDQL()));
		
		
		
		return $qb->getQuery()->getResult();
		
		
	}
//*/	
}