<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\AccessControlTrait;
use Dtkahl\AccessControl\UserInterface;

class TestUser implements UserInterface
{

  public function getGlobalRoles()
  {
    return ["member"];
  }

}