<?php namespace Dtkahl\AccessControl;

class AccessRole
{
    private $identifier;
    private $rights = [];
    private $object_rights = [];
    private $extended_role;

    /**
     * AccessRole constructor.
     * @param string $identifier
     * @param array $rights
     * @param array $object_rights
     * @param null $extended_role
     */
    public function __construct($identifier, array $rights, array $object_rights = [], $extended_role = null)
    {
        $this->identifier = $identifier;
        $this->rights = $rights;
        $this->object_rights = $object_rights;
        $this->extended_role = $extended_role;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param $rights
     * @param AccessObject|null $access_object
     * @throws AllowedException
     * @throws NotAllowedException
     * @return bool
     */
    public function checkRight($rights, AccessObject $access_object = null)
    {
        $rights = (array) $rights;

        if (is_null($access_object)) {
            if (count(array_intersect($rights, $this->rights)) == count($rights)) {
                throw new AllowedException;
            }
        } else {
            $identifier = $access_object->getIdentifier();
            if (
                array_key_exists($identifier, $this->object_rights)
                && count(array_intersect($rights, $this->object_rights[$identifier])) == count($rights)
            ) {
                throw new AllowedException;
            }
        }

        // TODO check in $extended_role
    }

}