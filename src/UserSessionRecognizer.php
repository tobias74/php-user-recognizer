<?php
namespace Zeitfaden\UserSession;


class UserSessionRecognizer
{

  use \Zeitfaden\Traits\UserRepositoryGetter,\Zeitfaden\Traits\ConfigGetter;

  protected $auth0;



  public function recognizeAuthenticatedUser()
  {
    
    $userData = $this->getAuth0()->getUser();

    if ($userData)
    {
      $userSession = $this->recognizeUserByAuth0Session();
    }
    else 
    {
      $userSession = new \Zeitfaden\UserSession\AnonymousUserSession();      
    }


    $userSession->setAuth0($this->getAuth0());
    return $userSession;
  }



  protected function getAuth0()
  {
    if (!$this->auth0)
    {
      $this->auth0 = new \Auth0\SDK\Auth0(array(
          'domain'        => $this->getConfig()->auth0WebsiteDomain,
          'client_id'     => $this->getConfig()->auth0WebsiteClientId,
          'client_secret' => $this->getConfig()->auth0WebsiteSecret,
          'redirect_uri'  => $this->getConfig()->auth0WebsiteCallback,
          'audience'      => 'https://'.$this->getConfig()->auth0WebsiteDomain.'/userinfo',
          'persist_id_token' => true,
          'persist_access_token' => true,
          'persist_refresh_token' => true
      ));    
    }
    
    return $this->auth0;
    
  }


  protected function recognizeUserByAuth0Session()
  {
      $userSmall = $this->getAuth0()->getUser();
      
      $auth0Api = new \Auth0\SDK\Auth0Api($this->getAuth0()->getIdToken(), $this->getConfig()->auth0WebsiteDomain);
      $userData = $auth0Api->users->get($userSmall['sub']);      
      

      $userSession = new \Zeitfaden\UserSession\Auth0Session();      
      try
      {
        $loggedInUser = $this->getUserRepository()->getOneByAuth0Id($userData['user_id']);
      }
      catch (\Zeitfaden\Exception\NoMatchException $e)
      {
        $loggedInUser = new User();
        $loggedInUser->setAuth0Id($userData['user_id']);
        $this->getUserRepository()->merge($loggedInUser);
      }
      
      $loggedInUser->profileImage = $userData['picture'];
      $loggedInUser->displayName = isset($userData['given_name']) ? $userData['given_name'] : $userData['nickname'];
      $this->getUserRepository()->merge($loggedInUser);
      
      $userSession->setLoggedInUserId($loggedInUser->getId());
      
      return $userSession;
 }



    
    
    
}