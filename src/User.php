<?php
namespace PhpUserRecognizer;

class User
{
    public $id;
    public $auth0Id;
    public $profileImage;
    public $displayName;
    
	public function getId()
	{
		return $this->id;
	}
    
	public function setId($val)
	{
		$this->id = $val;
	}
    
    public function getAuth0Id()
    {
        return $this->auth0Id;
    }
    
    public function setAuth0Id($val)
    {
        $this->auth0Id = $val;
    }
    
	public function getProfileImage()
	{
		return $this->profileImage;
	}
    
	public function setProfileImage($val)
	{
		$this->profileImage = $val;
	}

	public function getDisplayName()
	{
		return $this->displayName;
	}
    
	public function setDisplayName($val)
	{
		$this->displayName = $val;
	}

    
}