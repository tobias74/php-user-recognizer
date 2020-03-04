<?php

namespace PhpUserRecognizer;

class UserSessionRecognizer
{
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
        if (!$this->userRepository) {
            $this->userRepository = new \Speckvisit\Crud\MongoDb\Repository($this->getConfig(), new UserMapper());
        }

        return $this->userRepository;
    }

    public function setUserRepository($val)
    {
        $this->userRepository = $val;
    }

    public function getUserById($userId)
    {
        return $this->getUserRepository()->getById($userId);
    }

    public function getUserFactory()
    {
        return $this->userFactory;
    }

    public function setUserFactory($val)
    {
        $this->userFactory = $val;
    }

    public function recognizeAuthenticatedUser($session)
    {
        $auth0 = $this->getAuth0($session);
        $userSmall = $auth0->getUser();

        if ($userSmall) {
            $userSession = new Auth0Session();
            if ($this->userExistsLocally($userSmall['sub'])) {
                $loggedInUser = $this->getUserRepository()->getOneByAuth0Id($userSmall['sub']);
                $this->sporadicallyUpdateUserData($loggedInUser, $userSmall);
            } else {
                $loggedInUser = $this->introduceUserLocally($auth0->getIdToken(), $userSmall['sub']);
            }

            $userSession->setLoggedInUserId($loggedInUser->getId());
            $userSession->setLoggedInUser($loggedInUser);
        } else {
            $userSession = new AnonymousUserSession();
        }

        $userSession->setAuth0($auth0);

        return $userSession;
    }

    protected function getAuth0($symfonySession = null)
    {
        $config = array(
            'domain' => $this->getConfig()['auth0Domain'],
            'client_id' => $this->getConfig()['auth0ClientId'],
            'client_secret' => $this->getConfig()['auth0Secret'],
            'redirect_uri' => $this->getConfig()['auth0Callback'],
            'scope' => 'openid profile email',
        );

        if ($symfonySession) {
            $config['store'] = new SymfonySessionStore($symfonySession);
        } else {
            throw new \Exception('no symfony session given???????');
        }

        $auth0 = new \Auth0\SDK\Auth0($config);

        return $auth0;
    }

    protected function userExistsLocally($auth0UserId)
    {
        try {
            $loggedInUser = $this->getUserRepository()->getOneByAuth0Id($auth0UserId);

            return true;
        } catch (\Speckvisit\Crud\MongoDb\NoMatchException $e) {
            return false;
        }
    }

    protected function introduceUserLocally($idToken, $auth0UserId)
    {
        $auth0Api = new \Auth0\SDK\Auth0Api($idToken, $this->getConfig()['auth0Domain']);
        $userData = $auth0Api->users->get($auth0UserId);

        $newLocalUser = $this->getUserFactory()->create();
        $this->updateUserWithData($newLocalUser, $userData);

        return $newLocalUser;
    }

    protected function sporadicallyUpdateUserData($loggedInUser, $userData)
    {
        if (1 === rand(0, 50)) {
            $this->updateUserWithData($loggedInUser, $userData);
        }
    }

    protected function updateUserWithData($user, $userData)
    {
        $user->setAuth0Id($userData['sub']);
        $user->profileImage = $userData['picture'];
        $user->displayName = isset($userData['given_name']) ? $userData['given_name'] : $userData['nickname'];
        $this->getUserRepository()->merge($user);
    }
}
