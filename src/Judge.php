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
   * @param string|string[] $rights
   * @param ObjectInterface|null $object
   * @param UserInterface|null $user
   * @return bool
   */
  public function hasRight($rights, ObjectInterface $object = null, UserInterface $user = null)
  {
    $rights = (array) $rights;
    $user = $user ?: $this->_user;
    $global_roles = $user->getGlobalRoles();

    // check global rights
    foreach ($global_roles as $role) {
      if ($this->_config->has("global.$role.rights")) {
        if ($this->checkRightsInConfig($rights, $this->_config, "global.$role.rights")) {
          return true;
        }
      }
    }

    if ($object === null) {
      return false;
    }

    $object_config = $this->getObjectConfig($object);
    $object_identifier = $object_config->get("identifier");

    // check global rights
    foreach ($global_roles as $role) {
      if ($this->_config->has("global.$role.related_rights.$object_identifier")) {
        if ($this->checkRightsInConfig($rights, $this->_config, "global.$role.related_rights.$object_identifier")) {
          return true;
        }
      }
    }

    // check object rights
    foreach ($object->getObjectRoles($user) as $role) {
      if ($this->checkRightsInConfig($rights, $object_config, "roles.$role.rights")) {
        return true;
      }
    }

    // check object related rights
    foreach ($object->getRelatedObjects() as $related) {
      $related_config = $this->getObjectConfig($related);
      foreach ($related->getObjectRoles($user) as $role) {
        if ($this->checkRightsInConfig($rights, $related_config, "roles.$role.related_rights.$object_identifier")) {
          return true;
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
    return true; // TODO
  }

  /**
   * @param ObjectInterface $object
   * @throws \RuntimeException
   * @return Config
   */
  private function getObjectConfig(ObjectInterface $object)
  {
    $object_class = get_class($object);
    if ($this->_config->has("objects.$object_class")) {
      return new Config($this->_config->get("objects.$object_class"));
    }
    throw new \RuntimeException("Given object is not configured for judge.");
  }

  /**
   * @param array $rights
   * @param Config $config
   * @param $path
   * @return bool
   */
  private function checkRightsInConfig(array $rights, Config $config, $path)
  {
    return $config->has($path) && empty(array_diff($rights, $config->get($path)));
  }

}