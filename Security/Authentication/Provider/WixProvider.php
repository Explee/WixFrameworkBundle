<?php

namespace Wix\FrameworkBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Wix\FrameworkBundle\Security\Authentication\Token\WixToken;

class WixProvider implements AuthenticationProviderInterface
{
    protected $wix;
    private $userProvider;
    protected $userChecker;
    protected $createIfNotExists;

    public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker = null, $createIfNotExists, $wix)
    {
        if (null !== $userProvider && null === $userChecker) {
            throw new \InvalidArgumentException('$userChecker cannot be null, if $userProvider is not null.');
        }

        // if ($createIfNotExists && !$userProvider instanceof UserManagerInterface) {
        //     throw new \InvalidArgumentException('The $userProvider must implement UserManagerInterface if $createIfNotExists is true.');
        // }
        
        $this->wix = $wix;
        $this->userProvider = $userProvider;
        $this->userChecker = $userChecker;
        $this->createIfNotExists = $createIfNotExists;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            $newToken = new WixToken($token->getInstance(), $user, $user->getRoles());
            $newToken->setAttributes($token->getAttributes());

            return $newToken;
        } 

        if ($user) {
            $authenticatedToken = $this->createAuthenticatedToken($token->getInstance(), $user);
            $authenticatedToken->setAttributes($token->getAttributes());

            return $authenticatedToken;
        }

        throw new AuthenticationException('The Wix authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof WixToken;
    }

    protected function createAuthenticatedToken($instance, $uid)
    {
        if (null === $this->userProvider) {
            return new WixToken($instance, $uid, array());
        }
        // The user provider creates a user if there is none
        $user = $this->userProvider->loadUserByUsername($uid);
        $this->wix->establishCSRFTokenState();

        return new WixToken($instance, $user, $user->getRoles());
    }
}