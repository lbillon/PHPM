<?php

namespace PHPM\Bundle\Form\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use PHPM\Bundle\Form\EventListener\ConfigFormSubscriber;
use PHPM\Bundle\Form\Config\ConfigType;
use PHPM\Bundle\Form\LieuType;
use PHPM\Bundle\Form\EquipeType;
use PHPM\Bundle\Form\ConfianceType;
use PHPM\Bundle\Form\MaterielType;
class ManifType extends AbstractType
{
    protected $admin;
    protected $config;
    function __construct($admin, $config)
    {

        $this->config = $config;
        $this->admin = $admin;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
                ->add('configItems', 'collection',
                        array(	'type' => new ConfigType(),
                        		'allow_add' => true,
                                'allow_delete' => true,
                                'by_reference' => false,
                                'label' => 'Clés de Configuration',
                				'options'  => array('error_bubbling'=>true)));

//         $builder
//                 ->add('lieuItems', 'collection',
//                         array('type' => new LieuType(), 'allow_add' => true,
//                                 'allow_delete' => true,
//                                 'by_reference' => false,
//                                 'label' => 'Lieux'));

        $builder
                ->add('equipeItems', 'collection',
                        array('type' => new EquipeType(), 'allow_add' => true,
                                'allow_delete' => true,
                                'by_reference' => false,
                                'label' => 'Équipes',
                				'options'  => array('error_bubbling'=>true)));

        $builder
                ->add('confianceItems', 'collection',
                        array('type' => new ConfianceType(),
                                'allow_add' => true, 'allow_delete' => true,
                                'by_reference' => false,
                                'label' => 'Niveaux de Confiance',
                				'options'  => array('error_bubbling'=>true)));

        $builder
                ->add('materielItems', 'collection',
                        array('type' => new MaterielType(),
                                'allow_add' => true, 'allow_delete' => true,
                                'by_reference' => false,
                                'label' => 'Matériel',
                				'options'  => array('error_bubbling'=>true)));

        $form = $builder->getForm();

    }

    public function getName()
    {
        return 'phpm_bundle_maniftype';
    }

}
