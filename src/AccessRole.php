<?php namespace Dtkahl\AccessControl;

class AccessRole
{
    private $identifier;
    private $rights = [];
    private $extended_role;

    public function __construct($identifier, array $rights, $extended_role = null)
    {
        $this->identifier = $identifier;
        $this->rights = $rights;
        $this->extended_role = $extended_role;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function hasRight($identifier)
    {
        // TODO check in $rights
        // TODO check in $extended_role
    }

}