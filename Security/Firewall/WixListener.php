<?php

namespace Wix\FrameworkBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;

use Wix\FrameworkBundle\Security\Authentication\Token\WixToken;
use Wix\FrameworkComponent\InstanceDecoderInterface;

class WsseListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $decoder;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, InstanceDecoderInterface $decoder)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->decoder = $decoder;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Check the request
        $instance = $this->decoder->parse($request->get('instance');
        if($instance === null){
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }

        $token = new WixToken();
        $token->instance = $instance;
        $token->setUser($instance->getUid());
        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);

            return;
        } catch (AuthenticationException $failed) {
            // ... you might log something here

            // To deny the authentication clear the token. This will redirect to the login page.
            // Make sure to only clear your token, not those of other authentication listeners.
            // $token = $this->securityContext->getToken();
            // if ($token instanceof WixToken && $this->providerKey === $token->getProviderKey()) {
            //     $this->securityContext->setToken(null);
            // }
            // return;

            // Deny authentication with a '403 Forbidden' HTTP response
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);

        }

        // By default deny authorization
        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}