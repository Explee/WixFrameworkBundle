<?php

namespace Wix\FrameworkBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

use Wix\FrameworkComponent\Instance\Instance;

class WixToken extends AbstractToken
{
    protected $instance;

    public function __construct(Instance $instance, $uid = '', array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($uid);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);

        $this->instance = $instance;
    }

    /**
     * Get instance
     *
     * @param $instance
     * @return 
     */
    public function getInstance()
    {
        return $this->instance;
    }
    
    /**
     * Set instance
     *
     * @param $instance
     * @return
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
        return $this;
    }
    public function getCredentials()
    {
        return '';
    }
}