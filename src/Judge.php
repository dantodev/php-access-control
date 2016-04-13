<?php namespace Dtkahl\AccessControl;

class Judge
{

  private $_config;
  private $_user;

  /**
   * @param array $config
   * @param UserInterface|null $user
   */
  public function __construct(array $config, UserInterface $user)
  {
    $this->_config = new Map($config, false, true);
    // TODO check structur
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

    /** @var Map $global_config */
    $global_config = $this->_config->get("global");
    $user = $user ?: $this->_user;

    foreach ($user->getGlobalRoles() as $role) {
      if ($global_config->has($role)) {
        /** @var Collection $role_rights */
        $role_rights = $global_config->get($role);
        if (!$role_rights->isEmpty() && $role_rights->hasValue($right)) {
          return true;
        }
      }
    }

    if ($object === null) {
      return false;
    }

    /**
     * @var Map $object_config
     * @var Map $object_roles_config
     */
    $object_config = $this->getObjectConfig($object);
    $object_roles_config = $object_config->get("roles");

    foreach ($object->getObjectRoles($user) as $role) {
      if ($object_roles_config->has($role)) {
        /**
         * @var Map $role_config
         * @var Collection $role_rights
         */
        $role_config = $object_roles_config->get($role);
        $role_rights = $role_config->get("rights");

        if (!$role_rights->isEmpty() && $role_rights->hasValue($right)) {
          return true;
        }
      }
    }

    foreach ($object->getRelatedObjects() as $related) {
      /**
       * @var Map $related_config
       * @var Map $related_roles_config
       */
      $related_config = $this->getObjectConfig($related);
      $related_roles_config = $related_config->get("roles");

      foreach ($related->getObjectRoles($user) as $role) {
        if ($related_roles_config->has($role)) {
          /**
           * @var Map $role_config
           * @var Map $related_config
           * @var Collection $role_rights
           */
          $role_config = $related_roles_config->get($role);
          $related_config = $role_config->get("related_rights");
          $role_rights = $related_config->get($object_config->get("identifier"));
          if (!$role_rights->isEmpty() && $role_rights->hasValue($right)) {
            return true;
          }
        }
      }
    }

    return false;

    /*
      !! check global roles !!

      $user->hasRight("write", $blog); // author : true
       - $right : "write"
       - $object : "blog"
        - each $object->getObjectRoles() // ["author"]
          - $role : "author"
          - $right in_array $object[$role] // true
       - each $object->getRelatedObjects() // []

      $user->hasRight("remove" $comment); // author
       - $right : "remove"
       - $object : "comment"
       - each $object->getObjectRoles() // []
       - each $object->getRelatedObjects() // [$blog]
         - $related : "blog"
         - roles for $related: // ["author"]
           - $role = "author"
             - $right in_array $related[$role][$object] // true
     */
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
   * @return Map|null
   */
  public function getObjectConfig(ObjectInterface $object)
  {
    if ($object === null) {
      return null;
    }

    /** @var Map $objects_config */
    $objects_config = $this->_config->get("objects");

    $object_class = get_class($object);
    if ($objects_config->has($object_class)) {
      return $objects_config->get($object_class);
    }
    throw new \RuntimeException("Given object is not configured for judge.");
  }

}