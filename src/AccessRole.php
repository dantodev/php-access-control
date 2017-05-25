<?php namespace Dtkahl\AccessControl;

class AccessRole
{
    private $identifier;
    private $rights = [];
    private $extended_role;

    /**
     * AccessRole constructor.
     * @param string $identifier
     * @param array $rights
     * @param AccessRole|null $extended_role
     */
    public function __construct($identifier, array $rights, AccessRole $extended_role = null)
    {
        $this->identifier = $identifier;
        $this->rights = $this->prepareRights($rights);
        $this->extended_role = $extended_role;
    }

    private function prepareRights($rights)
    {
        $prepared = [];
        foreach ($rights as $key=>$value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $prepared[] = "$key.$item";
                }
            } else {
                $prepared[] = $value;
            }
        }
        return $prepared;
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
     */
    public function checkRight($rights, AccessObject $access_object = null)
    {
        $rights = (array) $rights;

        if (count(array_intersect($rights, $this->rights)) == count($rights)) {
            throw new AllowedException;
        }

        if (!is_null($access_object)) {
            if ($this->extended_role instanceof AccessRole) {
                $this->extended_role->checkRight($rights, $access_object);
            }
        }
    }

}