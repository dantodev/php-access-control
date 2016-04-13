<?php namespace Dtkahl\AccessControl;

interface ObjectInterface
{

  /**
   * Returns an array of roles related through this object instance to given user instance.
   * Keep in mind that there are no recursive checks by AccessControl. You have to do this on your own in this method.
   *
   * @param UserInterface $user
   * @return string[]
   */
  public function getObjectRoles(UserInterface $user);

  /**
   * Returns an array of relevant objects related to this object instance.
   *
   * @return ObjectInterface[]
   */
  public function getRelatedObjects();

}