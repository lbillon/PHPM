<?php

namespace AssoMaker\ComptesPersoBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TransactionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends EntityRepository
{
	public function getOrgaBalance($orgaId){
		$entities = $this->getEntityManager()->getRepository('AssoMakerComptesPersoBundle:Transaction')->findByOrga($orgaId);

		if(!$entities){
			return 0;
		}else{
			$balance = 0;
			foreach ($entities as $transaction){
				$balance+=$transaction->getAmount();
			}
			return  $balance;

		}
	}	
	
	public function getComptes(){
   
	    $entities = $this->getEntityManager()
	    ->createQuery("SELECT o FROM AssoMakerBaseBundle:Orga o WHERE o.privileges >=1 ORDER BY o.prenom ")
	    ->getResult();
	    
	    
	    $comptes = array();
	    //création du Json de retour selon le modèle définit dans la spec (cf wiki)
	    foreach ($entities as $orga) {
	    
	        $comptes[] = array(
	                "id" => $orga->getId(),
	                "name" => $orga->__toString(),
	                "balance"=> $this->getOrgaBalance($orga->getId())
	        );
	    }
	    return $comptes;
	}
	
	public function getTransactionsArray(){
	     
	    $entities = $this->getEntityManager()
	    ->createQuery("SELECT t FROM AssoMakerComptesPersoBundle:Transaction t ORDER BY t.commitDate DESC ")
	    ->getResult();
	     
	     
	    $transactions = array();
	    //création du Json de retour selon le modèle définit dans la spec (cf wiki)
	    foreach ($entities as $transaction) {
	         
	        $transactions[] = $transaction->toArray();
	    }
	    return $transactions;
	}
	   
}
