<?php namespace Dtkahl\AccessControl;

use Dtkahl\SimpleConfig\Config;

class Judge
{

  private $_config;
  private $_user;

  /**
   * @param array|Config $config
   * @param UserInterface|null $user
   */
  public function __construct($config, UserInterface $user)
  {
    $this->_config = $config instanceof Config ? $config : new Config($config);
    // TODO check structure
    $this->_user = $user;
  }

  /**
   * @param string $right
   * @param ObjectInterface|null $object
   * @param UserInterface|null $user
   * @return bool
   */
  public function hasRight($right, ObjectInterface $object = null, UserInterface $user = null)
  {
    // TODO check multiple rights

    $user = $user ?: $this->_user;

    // check global rights
    foreach ($user->getGlobalRoles() as $role) {
      if ($this->_config->has("global.$role")) {
        if (in_array($right, $this->_config->get("global.$role"))) {
          return true;
        }
      }
    }

    if ($object !== null) {
      $object_config = $this->getObjectConfig($object);
      $object_identifier = $object_config->get("identifier");

      // check object rights
      foreach ($object->getObjectRoles($user) as $role) {
        if ($object_config->has("roles.$role.rights") && in_array($right, $object_config->get("roles.$role.rights"))) {
          return true;
        }
      }

      // check object relation rights
      foreach ($object->getRelatedObjects() as $related) {
        $related_config = $this->getObjectConfig($related);
        foreach ($related->getObjectRoles($user) as $role) {
          if ($related_config->has("roles.$role.related_rights.$object_identifier") &&
              in_array($right, $related_config->get("roles.$role.related_rights.$object_identifier"))) {
            return true;
          }
        }
      }
    }
    return false;
  }

  /**
   * @param string $role
   * @param string|ObjectInterface|null $object
   * @param UserInterface|null $user
   * @return bool
   */
  public function hasRole($role, ObjectInterface $object = null, UserInterface $user = null)
  {
    return true;
  }

  /**
   * @param ObjectInterface $object
   * @throws \RuntimeException
   * @return Config
   */
  public function getObjectConfig(ObjectInterface $object)
  {
    $object_class = get_class($object);
    if ($this->_config->has("objects.$object_class")) {
      return new Config($this->_config->get("objects.$object_class"));
    }
    throw new \RuntimeException("Given object is not configured for judge.");
  }

}