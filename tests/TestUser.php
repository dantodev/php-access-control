<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\AccessControlTrait;
use Dtkahl\AccessControl\UserAccessInterface;

class TestUser implements UserAccessInterface
{

    private $global_roles = [];

    public function __construct(array $global_roles)
    {
        $this->global_roles = $global_roles;
    }

    public function getGlobalRoles()
    {
        return $this->global_roles;
    }

}