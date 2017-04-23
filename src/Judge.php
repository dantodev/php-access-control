<?php namespace Dtkahl\AccessControl;

use Dtkahl\ArrayTools\Map;

class Judge
{
    private $global_roles;
    private $objects;
    private $user;

    /**
     * @param AccessRole[] $global_roles
     * @param AccessObject[] $objects
     * @param UserAccessInterface|null $user
     */
    public function __construct(array $global_roles, array $objects, UserAccessInterface $user = null)
    {
        $this->global_roles = new Map();
        $this->objects = new Map();
        $this->user = $user;

        foreach ($global_roles as $role) {
            $this->registerRole($role);
        }

        foreach ($objects as $object) {
            $this->registerObject($object);
        }
    }

    /**
     * @param AccessRole $role
     */
    public function registerRole(AccessRole $role)
    {
        $this->global_roles->set($role->getIdentifier(), $role);
    }

    /**
     * @param AccessObject $object
     */
    public function registerObject(AccessObject $object)
    {
        $this->objects->set($object->getIdentifier(), $object);
    }

    /**
     * @param string|string[] $rights
     * @param ObjectInterface|null $object
     * @param UserAccessInterface|null $user
     * @throws NotAllowedException
     * @return void
     */
    public function checkRight($rights, ObjectInterface $object = null, UserAccessInterface $user = null)
    {
        $rights = (array)$rights;
        $user = $user ?: $this->user;

        if (!$user instanceof UserAccessInterface) {
            throw new \InvalidArgumentException("No user for right check");
        }

        $user_roles = $this->global_roles->only($user->getGlobalRoles());

        try {
            if (is_null($object)) {
                $this->checkRightInRoles($rights, $user_roles);
            } else {
                $access_object = $this->getAccessObject($object);
                // check global roles related rights
                $this->checkRightInRoles($rights, $user_roles, $access_object);
                // check object rights
                $this->checkRightForObject($rights, $object, $user, $access_object);
            }
        } catch (AllowedException $allowedException) {
            return; // Alright, please do not look behind. Everything is fine. Just walk on.
        }
        throw new NotAllowedException;
    }

    private function checkRightForObject($rights, ObjectInterface $object, UserAccessInterface $user, AccessObject $original_access_object)
    {
        $access_object = $this->getAccessObject($object);

        $object_roles = $access_object->getRoles()->only($object->getObjectRoles($user));
        $this->checkRightInRoles($rights, $object_roles, $original_access_object);

        // do the same for all related objects
        $related_objects = $this->hydrateRelatedObjects($object);
        $related_objects->each(function ($identifier, ObjectInterface $object) use ($rights, $user, $original_access_object) {
            $this->checkRightForObject($rights, $object, $user, $original_access_object);
        });
    }

    /**
     * @param ObjectInterface $object
     * @throws \InvalidArgumentException
     * @return AccessObject
     */
    private function getAccessObject(ObjectInterface $object)
    {
        $access_object = $this->objects->get($object->getObjectIdentifier());
        if (!$access_object instanceof AccessObject) {
            throw new \InvalidArgumentException("The given object is not registered");
        }
        return $access_object;
    }

    private function hydrateRelatedObjects(ObjectInterface $object)
    {
        $object->getRelatedObjects();
        $map = new Map();
        foreach ($object->getRelatedObjects() as $related_object) {
            if (!$object instanceof ObjectInterface) {
                throw new \InvalidArgumentException("Related object does not implement ObjectInterface");
            }
            $map->set($related_object->getObjectIdentifier(), $related_object);
        }
        return $map;
    }

    private function checkRightInRoles($rights, Map $roles, AccessObject $access_object = null)
    {
        $roles->each(function ($identifier, AccessRole $role) use ($rights, $access_object) {
            $role->checkRight($rights, $access_object);
        });
    }

    /**
     * @param string|string[] $rights
     * @param ObjectInterface|null $object
     * @param UserAccessInterface|null $user
     * @return bool
     */
    public function hasRight($rights, ObjectInterface $object = null, UserAccessInterface $user = null)
    {
        try {
            $this->checkRight($rights, $object, $user);
        } catch (NotAllowedException $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param string $role
     * @param ObjectInterface|null $object
     * @param UserAccessInterface|null $user
     * @throws NotAllowedException
     * @return void
     */
    public function checkRole($role, ObjectInterface $object = null, UserAccessInterface $user = null)
    {
        $user = $user ?: $this->user;

        if (!$user instanceof UserAccessInterface) {
            throw new \InvalidArgumentException("No user for right check");
        }

        $user_roles = $this->global_roles->only($user->getGlobalRoles());

        try {
            if (is_null($object)) {
                $this->checkRoleInRoles($role, $user_roles);
            } else {
                $this->checkRoleForObject($role, $object, $user);
            }
        } catch (AllowedException $exception) {
            return; // same story like above, do not turn your head around sweaty
        }
        throw new NotAllowedException;
    }

    private function checkRoleForObject($role, ObjectInterface $object, UserAccessInterface $user)
    {
        $access_object = $this->objects->get($object->getObjectIdentifier());
        if (!$access_object instanceof AccessObject) {
            throw new \InvalidArgumentException("The given object is not registered");
        }

        // check object roles related rights
        $object_roles = $access_object->getRoles()->only($object->getObjectRoles($user));
        $this->checkRoleInRoles($role, $object_roles);

        // finally check related object roles related rights
        $related_objects = $this->hydrateRelatedObjects($object);
        $related_objects->each(function ($identifier, ObjectInterface $object) use ($role, $user) {
            $this->checkRoleForObject($role, $object, $user);
        });
    }

    private function checkRoleInRoles($role, Map $roles)
    {
        if ($roles->has($role)) {
            throw new AllowedException;
        }
    }

    /**
     * @param string $role
     * @param ObjectInterface|null $object
     * @param UserAccessInterface|null $user
     * @return bool
     */
    public function hasRole($role, ObjectInterface $object = null, UserAccessInterface $user = null)
    {
        try {
            $this->checkRole($role, $object, $user);
        } catch (NotAllowedException $exception) {
            return false;
        }
        return true;
    }

    /**
     * @param UserAccessInterface $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

}