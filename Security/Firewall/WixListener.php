<?php

namespace Wix\FrameworkBundle\Security\Firewall;

use Wix\FrameworkBundle\Security\Authentication\Token\WixToken;
use Wix\FrameworkComponent\InstanceDecoder;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

class WixListener extends AbstractAuthenticationListener
{
    protected function attemptAuthentication(Request $request)
    {
        var_dump('biboup');exit();
        $instance = $request->get('instance');

        try{
            $decoder = new InstanceDecoder(array(
                'application_key' => "132add06-14c5-7066-86e8-48b76f901d91",
                'application_secret' => "e019e0b3-c90e-4bf6-9d6d-2fc6b5e620cf"
                ));
            $instance = $decoder->parse($instance);
        } catch (\Exception $e) {
            return null;
        }
        return $this->authenticationManager->authenticate(new WixToken($instance, $instance->getUid(), array()));
    }
}