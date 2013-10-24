<?php

namespace Wix\FrameworkBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class WixFactory extends AbstractFactory
{
    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'wix';
    }

    protected function getListenerId()
    {
        return 'wix_framework.security.authentication.listener';
    }

    protected function getAuthProviderId()
    {
        return 'wix_framework.security.authentication.provider';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {

        $providerId = $this->getAuthProviderId() . '.' . $id;
        $definition = $container
            ->setDefinition($providerId, new DefinitionDecorator($this->getAuthProviderId()))
            ->replaceArgument(0, new Reference($userProviderId))
        ;
        // $authProviderId = 'fos_facebook.auth.'.$id;

        // $definition = $container
        //     ->setDefinition($authProviderId, new DefinitionDecorator('fos_facebook.auth'))
        //     ->replaceArgument(0, $id);

        // with user provider
        // if (isset($config['provider'])) {
        //     $definition
        //         ->addArgument(new Reference($userProviderId))
        //         ->addArgument(new Reference('security.user_checker'))
        //         ->addArgument($config['create_user_if_not_exists'])
        //     ;
        // }

        return $providerId;
    }
}