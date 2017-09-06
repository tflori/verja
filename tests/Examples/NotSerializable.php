<?php

namespace Verja\Test\Examples;

class NotSerializable implements \Serializable
{
    public function serialize()
    {
        throw new \LogicException('You cannot serialize or unserialize NotSerializable instances');
    }

    public function unserialize($serialized)
    {
        throw new \LogicException('You cannot serialize or unserialize NotSerializable instances');
    }
}
