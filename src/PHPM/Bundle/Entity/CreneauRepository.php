<?php

namespace PHPM\Bundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;


/**
 * CreneauRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CreneauRepository extends EntityRepository
{
	
	public function getCreneauxParJour($orga)
	{
	
		$entities= $this->getEntityManager()
		->createQuery("
		SELECT c FROM PHPMBundle:Creneau c
		JOIN c.disponibilite d
		JOIN d.orga o
		WHERE d.orga = :orga_id
		 ")
		 ->setParameter('orga_id', $orga->getId())
		->getResult();
		 
		
		
		$result=array();
		foreach ($entities as $entity){
			$dow = $entity->getDebut()->format('w');
			
			$result[$dow][$entity->getId()] = $entity;
		
		}
		
		
		return $result;
		
	}
	
	
	public function getCreneauxParJour2($orga_id){
		$conn = $this->_em->getConnection();
		//$orga_id=$orga->getId();
	
		$sql = "SELECT c.id, WEEKDAY(c.debut) d FROM Creneau c LEFT JOIN Disponibilite d ON c.disponibilite_id=d.id LEFT JOIN Orga o ON d.orga_id=o.id ";
	
		$rows = $conn->fetchAll($sql);
		
		foreach ($rows as $row){
			var_dump($c);
			$co=$c[0];
				
			$a[$c['w']][$co->getId()]=$co;
			//$a[$c['w'][($c[0])->getId()]]=$c;
				
		}
		
		//$rows = $conn->prepare($sql)->execute();
	
	
		return $rows;
	}
	
	public function getCreneauxParJourNative($orga_id){
		
		
		$rsm = new ResultSetMapping;
		$rsm->addEntityResult('PHPM\Bundle\Entity\Creneau', 'c');
		$rsm->addFieldResult('c', 'id', 'id');
		$rsm->addFieldResult('c', 'debut', 'debut');
		$rsm->addFieldResult('c', 'fin', 'fin');
		$rsm->addFieldResult('c', 'id', 'id');
		$rsm->addJoinedEntityResult("PHPM\Bundle\Entity\PlageHoraire", "p", "c", "plageHoraire");
		
		
		$rsm->addScalarResult('d', 'd');
		
		
		$query = $this->_em->createNativeQuery(
		'SELECT c.*, p.id as p_id, WEEKDAY(c.debut) d 
		FROM Creneau c 
		LEFT JOIN PlageHoraire p ON c.plageHoraire_id=p.id
		ORDER BY d', $rsm);
		
		
		$creneaux = $query->getResult();
		var_dump($creneaux);
		exit();
		
		foreach ($creneaux as $c){
			var_dump($c);
			$co=$c[0];
			
			$a[$c['w']][$co->getId()]=$co;
			//$a[$c['w'][($c[0])->getId()]]=$c;
			
		}
		var_dump($a);
		exit();
		
		return $a;
		
		
		
		
	}
	
	public function getCreneauxCompatibleWithCriteria($niveau_confiance, $permis, $duree, $orgaId, $plage, $jour, $date_time)
	{
		// bien filtrer pour ne prendre que les tâches prêtes pour affectation (statut = 3)
		// viré le reliquat "confiance"
	    $dql = 'SELECT c FROM PHPMBundle:Creneau c JOIN c.plageHoraire p JOIN p.tache t LEFT JOIN c.equipeHint eh LEFT JOIN eh.confiance ehc
	     LEFT JOIN c.orgaHint oh WHERE c.disponibilite IS NULL AND t.statut = 3 ';
	
	    if ($permis != '') {
	    	$dql .= "AND t.permisNecessaire = $permis ";
		}
	   
	    if ($niveau_confiance != '') {
	    	$valeurConfianceMin = $this->getEntityManager()->createQuery("SELECT c FROM PHPMBundle:Confiance c WHERE c.id = $niveau_confiance")->getSingleResult()->getValeur();
	    	
	    	$dql .= "AND ehc.valeur = $valeurConfianceMin "; // comportement strict
		}
	    
		// Filtre sur la durée, on utilise une fonction DQL custom
		// intval pour protéger notre code
 	    if ($duree != '') {
 	    	$dql .= 'AND (TIMEDIFF(c.debut, c.fin) <= '.intval($duree).') '; // TIMEDIFF, fonction DQL custom, fait la différence en minutes
		}
	    
	    if ($plage != '') {
		    $pref = json_decode($this->getEntityManager()->getRepository('PHPMBundle:Config')->findOneByField('manifestation_plages')->getValue(), TRUE);
		    $plage= $pref[$plage];
		    $debut = $plage['debut'];
		    $fin = $plage['fin'];
			
		    $dql .= "AND (c.debut <= '$fin') AND (c.fin >= '$debut') ";
	    }
	    
		if ($jour != '') {
			// $jour est automatiquement transformé en "$jour 00:00:00"
			// DQL n'implémente pas correctement DATE() (merci Doctrine de merde), 
			// on regarde donc par rapport à l'intervalle $jour 00:00:00 et $jour+1 00:00:00
			// on ne s'intéresse qu'à l'heure de début (question de ne pas oublier de créneaux)
			$dql.= "AND (c.debut >= '".$jour->format('Y-m-d')."') AND (c.debut < '".$jour->add(new \DateInterval('P1D'))->format('Y-m-d')."') ";
		}
		
	    if ($date_time != '') {
	    	$dql.= "AND (c.debut <= '$date_time') AND (c.fin >= '$date_time') ";
	    }
		
		// TODO : remettre
		/*if ($categorie != '') {
		$dql.= "AND t.categorie = $categorie ";
		}*/

	    if ($orgaId != '') {
	    	$orga =  $this->getEntityManager()->createQuery("SELECT o FROM PHPMBundle:Orga o WHERE o.id = $orgaId")->getSingleResult();
	    	$equipe = $orga->getEquipe()->getId();
	    	
	    	// on est dans les dispos de l'orga
		    $dql .= "AND (c.id IN 
		    (SELECT cin.id FROM PHPMBundle:Creneau cin, PHPMBundle:Orga oin JOIN oin.disponibilites doin 
		    WHERE oin = $orgaId AND ((cin.debut >= doin.debut) AND (cin.fin <= doin.fin )))) ";
			
		    // pas sur un créneau déjà affecté
		    $dql .= "AND (c.id NOT IN 
		    (SELECT ci.id FROM PHPMBundle:Creneau ci, PHPMBundle:Orga o JOIN o.disponibilites do JOIN do.creneaux co 
		    WHERE o = $orgaId AND ((ci.debut < co.fin) AND (ci.fin > co.debut )))) ";
		    
		    // Compatibilité équipe/niveau de confiance
		    // On retourne les créneaux pour lesquels
		    // 1 - L'orga est dans l'équipe equipeHint
		    // OU 2 - l'orga a une confiance supérieure ou égale à l'équipeHint 
		    $valeurConfianceOrga= $orga->getConfiance()->getValeur();
		    $dql.= "AND (ehc.valeur <= $valeurConfianceOrga OR c.equipeHint = $equipe)";
	       
	       	// on vérifie s'il y a une consigne  d'orga
	       	$dql .= "AND (c.orgaHint IS NULL OR c.orgaHint = $orgaId) ";
	    }
		
		// on dé-duplique
		$dql .= "GROUP BY c.plageHoraire, c.equipeHint, c.orgaHint ";
		
		if ($orga != '') {
			// ORDER BY doit être en dernier, après GROUP BY
			// on trie par priorité : orga, équipe puis confiance
			// TODO : equipeHint
			// TODO : confiance
// 			$dql .= "ORDER BY u DESC";
		}

	    $query = $this->getEntityManager()->createQuery($dql);
		
	    return $query->getResult();
	}
		
	
}