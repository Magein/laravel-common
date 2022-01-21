<?php

namespace Magein\Common;

use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class BaseMongoModel extends Eloquent
{
    use SoftDeletes;

    protected $connection = 'mongodb';

    /**
     * 增加是否是函数的判断
     *
     * @param string $value
     * @param bool $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {
        if (!is_array($value)) {
            return json_decode($value, !$asObject);
        }

        return $value;
    }
}
