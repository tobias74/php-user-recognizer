<?php
namespace PhpUserRecognizer;


class UserSessionRecognizer
{

  use \Zeitfaden\Traits\UserRepositoryGetter,\Zeitfaden\Traits\ConfigGetter;

  protected $auth0;
  protected $userRepository;


  public function __construct($config)
  {
    $this->config = $config;
  }
  
  protected function getConfig()
  {
    return $this->config;
  }

  protected function getUserRepository()
  {
    if (!$this->userRepository)
    {
      $this->userRepository = new \PhpCrudMongo\Repository($this->getConfig(), new UserMapper());
    }
    
    return $this->userRepository;
    
  }

  public function getUserById($userId)
  {
    return $this->getUserRepository()->getById($userId);
  }

  public function recognizeAuthenticatedUser()
  {
    
    $userData = $this->getAuth0()->getUser();

    if ($userData)
    {
      $userSession = $this->recognizeUserByAuth0Session();
    }
    else 
    {
      $userSession = new AnonymousUserSession();      
    }


    $userSession->setAuth0($this->getAuth0());
    return $userSession;
  }



  protected function getAuth0()
  {
    if (!$this->auth0)
    {
      $this->auth0 = new \Auth0\SDK\Auth0(array(
          'domain'        => $this->getConfig()->auth0Domain,
          'client_id'     => $this->getConfig()->auth0ClientId,
          'client_secret' => $this->getConfig()->auth0Secret,
          'redirect_uri'  => $this->getConfig()->auth0Callback,
          'audience'      => 'https://'.$this->getConfig()->auth0Domain.'/userinfo',
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
      
      $auth0Api = new \Auth0\SDK\Auth0Api($this->getAuth0()->getIdToken(), $this->getConfig()->auth0Domain);
      $userData = $auth0Api->users->get($userSmall['sub']);      
      

      $userSession = new Auth0Session();      
      try
      {
        $loggedInUser = $this->getUserRepository()->getOneByAuth0Id($userData['user_id']);
      }
      catch (\PhpCrudMongo\NoMatchException $e)
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