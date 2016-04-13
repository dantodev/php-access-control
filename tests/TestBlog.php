<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\ObjectInterface;
use Dtkahl\AccessControl\UserInterface;

class TestBlog implements ObjectInterface
{

  public function getObjectRoles(UserInterface $user)
  {
    // TODO: Implement getObjectRoles() method.
  }

  public function getRelatedObjects()
  {
    // TODO: Implement getRelatedObjects() method.
  }

}