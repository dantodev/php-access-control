<?php namespace Dtkahl\AccessControl;

interface UserInterface
{

  /**
   * Returns an array of roles in connection with this user
   *
   * @return string[]
   */
  public function getGlobalRoles();

}