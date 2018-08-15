<?php
namespace PhpUserRecognizer;


class SymfonySessionStore implements \Auth0\SDK\Store\StoreInterface
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
        $value = $this->symfonySession->get($key, $default);
        return $value;
    }


    public function delete($key)
    {
        return $this->symfonySession->remove($key);
    }



    // these are not part of the interface above
    
    public function getId()
    {
        return $this->symfonySession->getId();
    }
    
}