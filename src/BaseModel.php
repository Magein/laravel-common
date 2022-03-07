<?php

namespace Magein\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;
use magein\tools\common\Variable;


/**
 * @method static firstOrCreate($where, $params = [])
 * @method static updateOrCreate($where, $params = [])
 * @method static first()
 * @method static where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static whereDate($column, $value)
 * @method static whereBetween($column, $value)
 * @method static whereIn($column, $value)
 * @method static find($primary_key)
 */
class BaseModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public static function __callStatic($name, $arguments)
    {
        $underline = strripos($name, '_');
        if ($underline >= 0) {
            $field = substr($name, $underline + 1);
            if (empty($field) || empty($arguments)) {
                return null;
            }
            $field = Variable::instance()->underline($field);
            $value = $arguments[0] ?? null;
            if (empty($value)) {
                return null;
            }
            $params = $arguments[1] ?? [];
            if (!is_array($params)) {
                return null;
            }
            $params[$field] = $value;
            if ($underline === 0) {
                return static::where($params)->first();
            } elseif ($underline === 1) {
                return static::where($params)->get();
            } elseif ($underline === 2) {
                $page_size = $params['page_size'] ?? 15;
                unset($params['page_size']);
                return static::where($params)->paginate($page_size);
            }
        }

        return parent::__callStatic($name, $arguments);
    }

    /**
     * 属性转化设置成array json 进行转化
     *
     * 0 = [0]
     * true、false、'' = ''
     * 1、'1' = [1]
     *
     * @param $value
     * @return false|string
     */
    protected function asJson($value)
    {
        if ($value === 0 || $value === "0") {
            $value = [0];
        } elseif (empty($value) || is_bool($value)) {
            return '';
        } elseif (is_int($value) || is_string($value)) {
            $value = [$value];
        }

        return json_encode($value);
    }

    protected function asIntJson($value)
    {
        if (is_array($value)) {
            $value = array_unique($value);
            $value = $value ? array_reduce($value, function ($value, $item) {
                $value[] = intval($item);
                return $value;
            }) : [];
        }
        return $this->asJson($value);
    }

    public function fromJson($value, $asObject = false)
    {
        if (empty($value) || $value == '[]' || $value == '""' || $value === "''") {
            return [];
        }

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
    public function getCreatedAttribute(): string
    {
        $created_at = $this->attributes['created_at'] ?? '';

        if ($created_at) {
            return Date::parse($created_at)->diffForHumans();
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
    public function getUpdatedAttribute(): string
    {
        $updated_at = $this->attributes['updated_at'] ?? '';

        if ($updated_at) {
            return Date::parse($updated_at)->diffForHumans();
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
