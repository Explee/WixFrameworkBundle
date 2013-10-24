<?php

namespace Wix\FrameworkBundle\Security\Authentication\Provider;

use Wix\FrameworkBundle\Security\User\UserManagerInterface;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Wix\FrameworkBundle\Security\Authentication\Token\WixToken;

class WixProvider implements AuthenticationProviderInterface
{
    protected $wix;
    protected $userProvider;
    protected $userChecker;
    protected $createIfNotExists;

    public function __construct(UserProviderInterface $userProvider = null, UserCheckerInterface $userChecker = null, $createIfNotExists = false, $wix)
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
            $this->userChecker->checkPostAuth($user);

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

        try {
            $user = $this->userProvider->loadUserByUsername($uid);
            if ($user instanceof UserInterface) {
                $this->userChecker->checkPostAuth($user);
            }
        } catch (UsernameNotFoundException $ex) {
            if (!$this->createIfNotExists) {
                throw $ex;
            }

            $user = $this->userProvider->createUserFromUid($uid);
        }

        if (!$user instanceof UserInterface) {
            throw new AuthenticationException('User provider did not return an implementation of user interface.');
        }

        return new WixToken($instance, $user, $user->getRoles());
    }
}