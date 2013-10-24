<?php

namespace Wix\FrameworkBundle\Security\Firewall;

use Wix\FrameworkBundle\Security\Authentication\Token\WixToken;
use Wix\FrameworkComponent\InstanceDecoder;

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

        try{
            $decoder = new InstanceDecoder(array(
                'application_key' => "132add06-14c5-7066-86e8-48b76f901d91",
                'application_secret' => "e019e0b3-c90e-4bf6-9d6d-2fc6b5e620cf"
                ));
            $instance = $decoder->parse($instance);
        } catch (\Exception $e) {
            throw new AuthenticationException('The Wix authentication failed.');
        }
        return $this->authenticationManager->authenticate(new WixToken($instance, $instance->getUid(), array()));
    }
}