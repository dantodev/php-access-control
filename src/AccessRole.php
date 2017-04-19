<?php namespace Dtkahl\AccessControl;

class AccessRole
{
    private $identifier;
    private $rights = [];
    private $related_rights = [];
    private $extended_role;

    /**
     * AccessRole constructor.
     * @param string $identifier
     * @param array $rights
     * @param array $related_rights
     * @param null $extended_role
     */
    public function __construct($identifier, array $rights, array $related_rights = [], $extended_role = null)
    {
        $this->identifier = $identifier;
        $this->rights = $rights;
        $this->related_rights = $related_rights;
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
     * @return bool
     */
    public function hasRight($rights, AccessObject $access_object = null)
    {
        $rights = (array) $rights;

        if (is_null($access_object)) {
            return count(array_intersect($rights, $this->rights)) == count($rights);
        } else {
            $identifier = $access_object->getIdentifier();
            if (!array_key_exists($identifier, $this->related_rights)) { // TODO with Map
                return false;
            }
            return count(array_intersect($rights, $this->related_rights[$identifier])) == count($rights);
        }

        /**
         * Problem: Wenn Objekt übergeben dann müssen auch die normalen rechte geprüft werden oder sie müssen als
         * related rights definiert werden
         *
         * könnt eman beim init automatisch umbiegen, wäre dann nicht so schmerzhaft
         *
         * durch object roles kann man sowieso keine globalen rechte mehr erhalten
         */

        // TODO check in $extended_role
        // TODO check access object -> related rights
        return false;
    }

}