<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\AccessControlTrait;
use Dtkahl\AccessControl\UserInterface;

class TestUser implements UserInterface
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