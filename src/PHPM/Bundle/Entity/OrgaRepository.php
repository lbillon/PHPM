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
	public function getOrgasWithCriteria($permis, $maxDateNaissance, $plage_id, $niveau_confiance)
	{
	
		$qb = $this->getEntityManager()->createQueryBuilder();
		$expr = $qb->expr();
	
		$andx = $expr->andx(
		$expr->eq('o.statut','1'),
		$expr->eq('d.orga','o'),
		
		$expr->neq('d.orga','0')		
		);
	
	
	
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
	
	
	
	
	
		$qb
		->select('o')
	
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
	
	
//	getOrgasWithCriteriaTache numéro 2 pour gérer le tache id
//*
public function getOrgasWithCriteriaTache($permis, $maxDateNaissance, $tache_id, $plage_id, $niveau_confiance)
	{
		
		$qb = $this->getEntityManager()->createQueryBuilder();
		$expr = $qb->expr();
	
		$andx = $expr->andx(
		
		
		
		$expr->eq('t.id',$tache_id),
		
		$expr->eq('d.orga','o'),
		$expr->neq('d.orga','0'),
		$expr->eq('co.disponibilite','d'),
		$expr->eq('ct.plageHoraire','p'),
		
		$expr->eq('p.tache','t'),
		
		
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
			$andx->add($expr->gte('o.confiance_id',$niveau_confiance));
		}
		
		
		
		
		
		$qb
		->select('o,ct')
		
		->from('PHPMBundle:Orga','o')
		
		
		
		->from('PHPMBundle:Disponibilite', 'd')
		->from('PHPMBundle:Creneau', 'co')
		->from('PHPMBundle:PlageHoraire', 'p')
		->from('PHPMBundle:Tache', 't')
		->from('PHPMBundle:Creneau', 'ct')
		
		
		->where($andx);
		
		exit(var_dump($qb->getQuery()->getDQL()));
		
		
		
		return $qb->getQuery()->getResult();
		
		
	}
*/	
}