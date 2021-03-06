<?php

namespace PhpUserRecognizer;

class AbstractUserSession
{
    protected $loggedInUserId = false;
    protected $loggedInUser;

    public function setAuth0($val)
    {
        $this->auth0 = $val;
    }

    public function getAuth0()
    {
        return $this->auth0;
    }

    public function login()
    {
        $this->getAuth0()->login();
    }

    public function logout()
    {
        $this->getAuth0()->logout();
    }

    public function setLoggedInUserId($val)
    {
        $this->loggedInUserId = $val;
    }

    public function getLoggedInUserId()
    {
        return $this->loggedInUserId;
    }

    public function setLoggedInUser($val)
    {
        $this->loggedInUser = $val;
    }

    public function getLoggedInUser()
    {
        return $this->loggedInUser;
    }

    public function isUserLoggedIn()
    {
        if (false !== $this->getLoggedInUserId()) {
            return true;
        } else {
            return false;
        }
    }

    public function hasAdminRole()
    {
        return false;
    }

    public function isOAuthSession()
    {
        return false;
    }
}
