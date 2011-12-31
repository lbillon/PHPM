<?php

namespace PHPM\Bundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TacheRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TacheRepository extends EntityRepository
{
	
	public function getTacheWithCriteria($duree, $categorie, $permis, $age, $orga_id, $plage_id, $niveau_confiance)
	{
	
		$qb = $this->getEntityManager()->createQueryBuilder()->select('t')->from('PHPMBundle:Tache','t');
	
		$query = $qb->getQuery();
	
	
		if($duree!='')
		{
			$qb->where($qb->expr()->lte('t.duree',$duree));
		}
		if($categorie !='')
		{
			$qb->where($qb->expr()->eq('t.categorie_id',$categorie));
		}
		if($permis!='')
		{
			$qb->where($qb->expr()->gte('t.permisNecessaire',$permis));
		}
		if($age !='')
		{
			$qb->where($qb->expr()->gte('t.ageNecessaire',$age));
		}
		
		
		
		
		if($orga_id !='')
		{
			$qb->where($qb->expr()->eq('t.orga_id',$orga_id));
		}
		if($plage_id !='')
		{
			$qb->where($qb->expr()->eq('t.$lage_id',$plage_id));
		}
		if($niveau_confiance !='')
		{
			$qb->where($qb->expr()->gte('t.confiance_id',$niveau_confiance));
		}
	
		return $qb->getQuery()->getResult();
	
	
	}
	
	
}