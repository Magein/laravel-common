<?php


namespace Magein\Common\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use magein\tools\common\Variable;

class MakeModelValidator extends Command
{
    /**
     * 根据表结构自动生成验证类
     * table_name需要输入完整的表名称
     *
     * php artisan model:validate table_name
     *
     * @var string
     */
    protected $signature = 'model:validate {name?} {--ignore}';

    protected $description = "创建模型的验证类";

    protected $help = "Notice：
    参数是数据库的表名称，需要写完整的、正确的表名称
Usage：
    php artisan model:validate members  创建Requests/MemberRequest.php
    php artisan model:validate companies  创建Requests/CompanyRequest.php
    php artisan model:validate member_orders  创建Requests/Member/MemberOrderRequest.php
    php artisan model:validate member_orders --ignore  创建Requests/MemberOrderRequest.php";

    private function help()
    {
        $this->info($this->getHelp());
        die();
    }

    public function handle()
    {
        $name = $this->argument('name');
        $ignore = $this->option('ignore');
        if (empty($name)) {
            $this->help();
        }

        $attrs = DB::select("show full columns from $name");

        if (empty($attrs)) {
            $this->error("Error:");
            $this->error("  没有找到{$name}表");
            $this->help();
        }

        $rules = [];
        $messages = [];
        foreach ($attrs as $attr) {
            $field = $attr->Field;
            $type = $attr->Type;
            $null = $attr->Null;
            $key = $attr->Key;
            $default = $attr->Default;
            $comment = $attr->Comment;
            if (in_array($field, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            $comments = explode(' ', $comment);
            if ($comments) {
                $comment = $comments[0];
            } else {
                $comment = '';
            }

            $rule = [
                'bail'
            ];


            //判断是不是主键
            if ($key == 'UNI') {
                $rule [] = "unique:$name";
                $messages["$field.unique"] = "{$comment}不能重复";
            }

            // 判断是不是为空
            if ($null == 'YES') {
                $rule [] = 'nullable';
            } elseif ($default === null) {
                // null 为 no 并且没有默认值，则视为必填
                $rule [] = 'required';
                $messages["$field.required"] = "请输入{$comment}";
            }

            /**
             * 从类型判断
             */
            if ($type == 'tinyint') {
                $rule [] = 'integer';
                $messages["$field.integer"] = "{$comment}只能是整数";

                $rule[] = 'in:0,1';
                $messages["$field.in"] = "{$comment}值错误";

            } elseif (preg_match('/int/', $type)) {
                $rule[] = 'integer';
            } elseif (preg_match('/decimal/', $type)) {
                $rule[] = 'numeric';
            } elseif (preg_match('/^char/', $type)) {
                $rule [] = "string";
                preg_match('/[0-9]+/', $type, $matches);
                $size = $matches[0] ?? '';
                if ($size) {
                    $rule[] = "size:$size";
                }
            } elseif (preg_match('/^varchar/', $type)) {
                $rule [] = "string";
                preg_match('/[0-9]+/', $type, $matches);
                $size = $matches[0] ?? 0;
                if ($size) {
                    $rule[] = "between:0,$size";
                }
            } elseif (in_array($type, ['timestamp', 'datetime'])) {
                $rule [] = "date";
            }

            /**
             * 一下是常用的字段进行的判断
             */
            switch ($field) {
                case 'email':
                    $rule [] = 'email';
                    break;
                case 'phone':
                    $rule [] = 'regex:/^1\d{10}/';
                    break;
                case 'password':
                    $rule [] = 'alpha_dash';
                    $rule [] = 'between:6,18';
                    break;
                case 'sex':
                case 'gender':
                    $rule [] = 'in:0,1,2';
                    break;
                case 'age':
                    $rule [] = 'between:0,130';
                    break;
                case 'id_card':
                case 'id_number':
                    $rule [] = 'regex:/^\d{17}.{1}/';
                    break;
                case 'logo':
                case 'avatar':
                case 'icon':
                case 'image':
                case 'thumb':
                case 'photo':
                case 'pic':
                case 'picture':
                case 'bg':
                    $rule [] = 'image';
                    break;
                case 'start_time':
                case 'begin_time':
                    $rule [] = 'date';
                case 'end_time':
                    $rule [] = 'date';
                    $rule [] = 'after:start_time';
            }

            // 订单编号、商品编号等验证
            if (preg_match('/_no$/', $field)) {
                $rule [] = 'alpha_num';
            }

            $rule = array_unique($rule);

            $rules[$field] = implode('|', $rule);
        }

        $rules_str = "\n";
        foreach ($rules as $field => $rule) {
            $rules_str .= "            '$field' => '$rule',\n";
        }

        // 保存的路径
        $save_path = './app/Http/Requests';
        $namespace = 'namespace App\Http\Requests';

        if (!$ignore) {
            $params = explode('_', $name);
            $dir = $params[0] ?? '';
            if ($dir) {
                $dir_name = Variable::instance()->pascal($dir);
                $save_path .= '/' . $dir_name;
                $namespace = "$namespace\\{$dir_name}";
            }
        }

        if (!is_dir($save_path) && !mkdir($save_path, 0777, true)) {
            $this->error('Error:');
            $this->error("  创建目录失败:$save_path");
        }

        $class_name = $name;
        if (preg_match('/s$/', $class_name)) {
            $class_name = substr($class_name, 0, -1);
        }

        $class_name = Variable::instance()->pascal($class_name) . 'Request';

        $filename = "{$save_path}/{$class_name}.php";
        $rules_str = rtrim($rules_str, "\n");

        $content = <<<EOF
<?php

$namespace;

use Illuminate\Foundation\Http\FormRequest;

class {$class_name} extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules():array
    {
        return [$rules_str
        ];
    }
}
EOF;

        file_put_contents($filename, $content);

        $this->info('make validate successful');
    }
}
