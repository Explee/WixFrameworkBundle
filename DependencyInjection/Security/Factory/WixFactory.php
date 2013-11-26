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

        return $providerId;
    }
}