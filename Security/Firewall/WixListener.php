<?php

namespace Wix\FrameworkBundle\Security\Firewall;

use Wix\FrameworkBundle\Security\Authentication\Token\WixToken;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

class WixListener extends AbstractAuthenticationListener
{
    protected function attemptAuthentication(Request $request)
    {
        $instance = $request->get('instance');
        if($instance === null){
            throw new AuthenticationException('The authentication failed.');
        }

        return $this->authenticationManager->authenticate(new WixToken($instance, '', array()));
    }
}