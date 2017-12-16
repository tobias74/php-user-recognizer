<?php
namespace Zeitfaden\UserSession;

class Auth0Session extends AbstractUserSession
{
    public function getProviderId()
    {
        return "auth0";
    }

}





