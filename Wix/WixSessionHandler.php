<?php

namespace Wix\FrameworkBundle\Wix;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class WixSessionHandler
{
    const PREFIX = '_wix_';

    protected $session;
    protected $prefix;
    protected $app_key;
    protected static $kSupportedKeys = array('state');

    /**
     * @param array $app_key
     * @param Session $session
     * @param string $prefix
     */
    public function __construct($app_key, Session $session, $prefix = self::PREFIX)
    {
        $this->session = $session;
        $this->prefix  = $prefix;
        $this->app_key  = $app_key;


    }

    public function establishCSRFTokenState()
    {
        if ($this->getState() === null) {
            $this->setState(md5(uniqid(mt_rand(), true)));
        }
    }

    /**
     * Stores the given ($key, $value) pair, so that future calls to
     * getPersistentData($key) return $value. This call may be in another request.
     *
     * @param string $key
     * @param array $value
     *
     * @return void
     */
    protected function setPersistentData($key, $value)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to setPersistentData.');

            return;
        }

        $this->session->set($this->constructSessionVariableName($key), $value);
    }

    /**
     * Get the data for $key, persisted by BaseFacebook::setPersistentData()
     *
     * @param string $key The key of the data to retrieve
     * @param boolean $default The default value to return if $key is not found
     *
     * @return mixed
     */
    protected function getPersistentData($key, $default = false)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to getPersistentData.');

            return $default;
        }

        $sessionVariableName = $this->constructSessionVariableName($key);
        if ($this->session->has($sessionVariableName)) {
            return $this->session->get($sessionVariableName);
        }

        return $default;
    }

    /**
     * Clear the data with $key from the persistent storage
     *
     * @param string $key
     * @return void
     */
    protected function clearPersistentData($key)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to clearPersistentData.');

            return;
        }

        $this->session->remove($this->constructSessionVariableName($key));
    }

    /**
     * Clear all data from the persistent storage
     *
     * @return void
     */
    protected function clearAllPersistentData()
    {
        foreach ($this->session->all() as $k => $v) {
            if (0 !== strpos($k, $this->prefix)) {
                continue;
            }

            $this->session->remove($k);
        }
    }

    protected function constructSessionVariableName($key)
    {
        return $this->prefix.implode(
            '_',
            array(
                'wix',
                $this->app_key,
                $key,
            )
        );
    }

    private function getState()
    {
        return $this->getPersistentData('state', null);
    }

    private function setState($state)
    {
        $this->setPersistentData('state', $state);
    }
}
