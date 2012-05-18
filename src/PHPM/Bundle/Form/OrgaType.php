<?php

namespace PHPM\Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use PHPM\Bundle\Entity\EquipeRepository;

class OrgaType extends AbstractType
{
    protected $admin;
    protected $config;
    protected $forcedConfiance;
    function __construct($admin,$config,$forcedConfiance){
        
            $this->config =$config;
            $this->admin =$admin;
            $this->forcedConfiance=$forcedConfiance;
    }
    
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $libellesPermis =  json_decode($this->config->getValue('manifestation_permis_libelles'),true);
        
    	$currentYear = date('Y');
    	$years = array(); 	
    	
    	for ($i=($currentYear-27);$i<=($currentYear-16);$i++){
    		array_push($years, $i);
    	
    	}
    	$builder
    	    ->add('prenom',null,array('label'=>'Prénom'))
            ->add('nom',null,array('label'=>'Nom'))
            ->add('surnom',null,array('label'=>'Surnom'))
            ->add('telephone',null,array('label'=>'Téléphone portable'))
            ->add('email',null,array('label'=>'Adresse email'))
            ->add('dateDeNaissance', 'birthday', array(
                    'label'=>'Date de naissance',
                    'years'=>$years,
                    'widget' => 'single_text',
                    'attr'=>array('class'=>'birthdaydp'),
                    'format' => 'yyyy-MM-dd'))
            ->add('datePermis', null, array(
                            'label'=>'Date de permis',
                            'widget' => 'single_text',
                            'format' => 'yyyy-MM-dd',
                            'attr'=>array('class'=>'datep')))
           	->add('anneeEtudes','choice',array(	'label'=>'Année d\'études',
                            			'required'=>false,
                            			'choices'=>array(1=>1,2,3,4,5,6,7,8,0=>'Autre')))
            ->add('departement','choice',array('label'=>'Département INSA', 'required'=>false, 'choices'=>array(
                    'PC'=>'PC','GMC'=>'GMC','GMD'=>'GMD', 'GMPP'=>'GMPP', 'IF'=>'IF', 'SGM'=>'SGM',
                    'GI'=>'GI', 'GE'=>'GE', 'TC'=>'TC', 'GCU'=>'GCU', 'BIM'=>'BIM', 'BIOCH'=>'BIOCH', 'GEN'=>'GEN', 'Autre'=>'Autre' 
                    )))
            ->add('groupePC',null,array('label'=>'Groupe (Premier Cycle Uniquement)',
            							'attr'=>array('placeHolder'=>'Groupe (si Premier Cycle)')))   
            
            ->add('commentaire')
            ->add('amis')
            ->add('celibataire','choice',array(	'label'=>'Célib\'?',
            									'required'=>false,
            									'choices'=>array('0'=>'Non','1'=>'Oui'),
            									'attr'=>array('class'=>'inline')))
            
    	    ;
    	    
    	    if($this->forcedConfiance){
    	    	$code=$this->forcedConfiance;
    	    	$builder->add('equipe','entity',array('label'=>'Équipe',
    	    			'class' => 'PHPMBundle:Equipe',
    	    			'query_builder' => function(EquipeRepository $er)use($code){return $er->findAllWithConfianceCode($code);}));
    	    }
    	    
    	    
    	if($this->admin){
        $builder
			
			->add('statut', 'choice',array('choices'=>array('0'=>'Inscrit','1'=>'Validé','2'=>'Complétement affecté'), 'read_only'=>!$this->admin))
			->add('privileges','choice',array('choices'=>array('0'=>'Visiteur','1'=>'Orga', '2'=>'Admin'),'read_only'=>!$this->admin))
			->add('equipe','entity',array('label'=>'Équipe','class' => 'PHPMBundle:Equipe'));
        
    	}
    }

    public function getName()
    {
        return 'phpm_bundle_orgatype';
    }
    
    
}
