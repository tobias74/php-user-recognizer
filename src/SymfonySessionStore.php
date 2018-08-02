<?php
namespace PhpUserRecognizer;


class SymfonySessionStore implements \Auth0\SDK\Store
{

    public function __construct($symfonySession)
    {
        $this->symfonySession = $symfonySession;
    }

    public function set($key, $value)
    {
        return $this->symfonySession->set($key, $value);
    }


    public function get($key, $default = null)
    {
        return $this->symfonySession->get($key, $default);
    }


    public function delete($key)
    {
        return $this->symfonySession->remove($key);
    }


    
}