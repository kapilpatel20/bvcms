<?php

namespace BvCmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CmsFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('name', 'text', array('required' => true))
                ->add('content', 'textarea', array(
                    'attr' => array('required' => true)
                ))
                
            ;
             
        $builder->add('status', 'choice', array(
                'choices' => array('Active'=>'Active', 'InActive'=>'Inactive',  'Deleted'=>'Deleted'),
                'multiple' => false,
                'expanded' => false,
              ));
    }

    public function getName() {
        return 'bv_cms_pages';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BvCmsBundle\Entity\Pages'
        ));
    }

}
