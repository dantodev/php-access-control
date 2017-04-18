<?php namespace Dtkahl\AccessControl;

class AccessObject
{
    private $identifier;
    private $object_roles = [];

    public function __construct($identifier, array $object_roles)
    {
        $this->identifier = $identifier;
        $this->object_roles = $object_roles;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function registerRole(AccessRole $role)
    {
        $this->object_roles[] = $role;
    }

    public function getRoles($user)
    {
        // todo get Roles by user
    }
}