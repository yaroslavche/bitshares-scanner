<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AssetAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('symbol', TextType::class)
            ->add('objectId', TextType::class)
            ->add('precision', TextType::class)
            ->add('price', TextType::class)
            ->add('volume', TextType::class)
            ->add('marketCap', TextType::class)
            ->add('type', TextType::class)
            ->add('currentSupply', TextType::class)
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('symbol')
            ->add('objectId')
            ->add('precision')
            ->add('price')
            ->add('volume')
            ->add('marketCap')
            ->add('type')
            ->add('currentSupply')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('symbol')
            ->add('objectId')
            ->add('precision')
            ->add('price')
            ->add('volume')
            ->add('marketCap')
            ->add('type')
            ->add('currentSupply')
        ;
    }
}
