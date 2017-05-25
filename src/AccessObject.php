<?php namespace Dtkahl\AccessControl;

use Dtkahl\ArrayTools\Map;

class AccessObject
{
    private $identifier;
    private $object_roles;

    /**
     * AccessObject constructor.
     * @param string $identifier
     * @param AccessRole[] $object_roles
     */
    public function __construct($identifier, array $object_roles)
    {
        $this->identifier = $identifier;
        $this->object_roles = new Map();

        foreach ($object_roles as $role) {
            $this->registerRole($role);
        }
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param AccessRole $role
     * @return $this
     */
    public function registerRole(AccessRole $role)
    {
        $this->object_roles->set($role->getIdentifier(), $role);
        return $this;
    }

    /**
     * @return Map
     */
    public function getRoles()
    {
        return $this->object_roles;
    }
}