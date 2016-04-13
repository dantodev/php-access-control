<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\ObjectInterface;
use Dtkahl\AccessControl\UserInterface;

class TestBlog implements ObjectInterface
{

  private $object_roles = [];
  private $related_object = [];

  public function __construct(array $object_roles, array $related_object)
  {
    $this->object_roles = $object_roles;
    $this->related_object = $related_object;
  }

  public function getObjectRoles(UserInterface $user)
  {
    return $this->object_roles;
  }

  public function getRelatedObjects()
  {
    return $this->related_object;
  }

}