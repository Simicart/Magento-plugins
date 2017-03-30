<?php

namespace Simi\Simipromoteapp\Helper;

class Db extends Data
{

    public function getDbResource()
    {
        return $this->resource;
    }

    public function getDbTableName()
    {
        return $this->getDbResource()->getTableName('simipromoteapp');
    }
}
