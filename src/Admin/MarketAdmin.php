<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MarketAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('pair', TextType::class)
            ->add('baseAssetId', TextType::class)
            ->add('quoteAssetId', TextType::class)
            ->add('volume', TextType::class)
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('pair')
            ->add('baseAssetId')
            ->add('quoteAssetId')
            ->add('volume')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('pair')
            ->add('baseAssetId')
            ->add('quoteAssetId')
            ->add('volume')
        ;
    }
}
