<?php
namespace Zeitfaden\UserSession;

class PhpUserSession extends AbstractUserSession
{
    public function getProviderId()
    {
        return "session";
    }
    

}





