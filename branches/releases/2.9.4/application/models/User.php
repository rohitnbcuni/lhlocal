<?php

class User
{
    const ID_UNKNOWN = 'anonymous user';

    protected $_data = array(
        'id' => null
        );

    public function __construct($id = null)
    {
        if (null !== $id) {
            $this->_data['id'] = $id;
        }
    }

    public function getId()
    {
        if (null === $this->_data['id']) {
            return self::ID_UNKNOWN;
        } else {
            return $this->_data['id'];
        }
    }
}
