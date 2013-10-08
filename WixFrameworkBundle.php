<?php

namespace Wix\FrameworkBundle;

use Wix\FrameworkBundle\DependencyInjection\Security\Factory\WixFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WixFrameworkBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WixFactory());
    }
}
