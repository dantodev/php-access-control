<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\ObjectInterface;
use Dtkahl\AccessControl\UserInterface;

class TestComment implements ObjectInterface
{

  public function getObjectRoles(UserInterface $user)
  {
    return [];
  }

  public function getRelatedObjects()
  {
    return [new TestBlog()];
  }

}