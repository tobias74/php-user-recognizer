<?php
namespace PhpUserRecognizer;


class UserMapper
{
    
    public function getCollectionName()
    {
        return "users";
    }
    
    protected function produceUserObject()
    {
        return new User();
    }
    
    protected function getMap()
    {
        return array(
            'id' => 'id',
            'auth0Id' => 'auth0Id',
            'profileImage' => 'profileImage',
            'displayName' => 'displayName'
        );
    }
    
    public function getColumnForField($fieldName)
    {
        $map = $this->getMap();
        return $map[lcfirst($fieldName)];
    }
    
    public function instantiate($document)
    {
        $resultHash = json_decode(\MongoDB\BSON\toJSON(\MongoDB\BSON\fromPHP($document)),true);
        $user = $this->produceUserObject();
        $user->setId($resultHash['id']);
        $user->setAuth0Id($resultHash['auth0Id']);
        $user->profileImage = isset($resultHash['profileImage']) ? $resultHash['profileImage'] : false;
        $user->displayName = isset($resultHash['displayName']) ? $resultHash['displayName'] : false;

        return $user;
    }
    
    public function mapToDocument($user)
    {
         $document = array(
            'id' => $user->id,
            'auth0Id' => $user->auth0Id,
            'profileImage' => $user->profileImage,
            'displayName' => $user->displayName
        );
        
        return $document;
        
    }
    
}