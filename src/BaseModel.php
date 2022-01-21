<?php

namespace Magein\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;


/**
 * @method static firstOrCreate($where, $params = [])
 * @method static updateOrCreate($where, $params = [])
 * @method static first()
 * @method static where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static whereDate($column, $value)
 * @method static whereBetween($column, $value)
 * @method static find($primary_key)
 */
class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 当传递一个数组的时候，调用fromJson会出现错误，所以重写了
     * @param string $value
     * @param false $asObject
     * @return array|mixed|string
     */
    public function fromJson($value, $asObject = false)
    {
        if (is_array($value)) {
            return $value;
        }

        return parent::fromJson($value, $asObject);
    }

    /**
     * @return string
     */
    public function getCreatedAtAttribute(): string
    {
        $created_at = $this->attributes['created_at'] ?? '';

        if ($created_at) {
            return Date::parse($created_at)->format('Y-m-d H:i');
        }

        return '';
    }

    /**
     * @return string
     */
    public function getUpdateAtAttribute(): string
    {
        $updated_at = $this->attributes['updated_at'] ?? '';

        if ($updated_at) {
            return Date::parse($updated_at)->format('Y-m-d H:i');
        }

        return '';
    }

    /**
     * @return string
     */
    public function getSexTextAttribute(): string
    {
        $sex = $this->attributes['sex'] ?? 0;

        $data = [
            0 => '保密',
            1 => '男',
            2 => '女',
        ];

        return $data[$sex] ?? '保密';
    }

    /**
     * 这里是回填使用的值，
     * @return array
     */
    public function getRegionAttribute(): array
    {
        $province_id = $this->attributes['province_id'] ?? '';
        $city_id = $this->attributes['city_id'] ?? '';
        $area_id = $this->attributes['area_id'] ?? '';

        return [
            intval($province_id),
            intval($city_id),
            intval($area_id)
        ];
    }
}
