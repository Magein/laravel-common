<?php

namespace Magein\Common\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use magein\tools\common\Variable;

class MakeModelProperty extends Command
{
    /**
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:property {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Models目录下的模型类类都追加上@property 属性名称';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function help()
    {
        $this->comment('请参考以下示列：');
        $this->info('  识别Models/User.php的命令             php artisan model:property user');
        $this->info('  识别Models/User/*.php的命令           php artisan model:property user_');
        $this->info('  识别Models/User/UserAction.php的命令  php artisan model:property user_action');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (empty($name)) {
            $this->help();
            exit(1);
        }

        if (preg_match('/_/', $name)) {
            $options = explode('_', $name);
        } else {
            $options = explode('_', $name);
        }

        if (count($options) == 1) {
            $filename = Variable::instance()->pascal($name);
            $files = glob("./app/Models/$filename.php");
        } else {
            $dir = $options[0] ?? '';
            $dir = Variable::instance()->pascal($dir);
            $filename = $options[1] ?? '';
            if (empty($filename)) {
                $files = glob("./app/Models/$dir/*.php");
            } else {
                $filename = Variable::instance()->pascal($filename);
                $files = glob("./app/Models/{$dir}/{$dir}{$filename}.php");
            }
        }

        if (empty($files)) {
            $this->error('没有加载到文件信息，请检查参数');
            $this->help();
            exit();
        }

        foreach ($files as $path) {
            $content = file_get_contents($path);
            $namespace = preg_replace(['/\.\/app/', '/\.php/', '/\//'], ['App', '', '\\\\'], $path);
            if (preg_match('/\* @property/', $content)) {
                $this->comment('continue file : ' . $path);
                continue;
            }
            $model = new $namespace();
            $attrs = Schema::getColumnListing($model->getTable());

            $property = '/**';
            $property .= "\n";

            $methods = '';
            $methods .= "\n";
            $method_params = function ($prefix, $name, $param) {
                if ($prefix == '__') {
                    $name = '\Illuminate\Database\Eloquent\Collection';
                } elseif ($prefix == '___') {
                    $name = '\Illuminate\Pagination\LengthAwarePaginator';
                } else {
                    $name = Variable::instance()->pascal($name);
                }
                return '* @method static ' . $name . '|null ' . $prefix . Variable::instance()->camelCase($param) . '($' . $param . ');' . "\n";
            };

            if ($attrs) {
                foreach ($attrs as $item) {
                    if (in_array($item, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                        continue;
                    }
                    $type = 'string';
                    if (preg_match('/id/', $item)) {
                        $type = 'integer';
                    }
                    $property .= " * @property $type $" . $item;
                    $property .= "\n";

                    if (preg_match('/_id|_no|phone|email$/', $item)) {
                        $methods .= $method_params('_', $name, $item);
                        $methods .= $method_params('__', $name, $item);
                        $methods .= $method_params('___', $name, $item);
                    }
                }
            }

            if (preg_match('/extends BaseModel/', $content)) {
                $property .= $methods;
            }

            $property .= " */";

            // 替换属性
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $content = preg_replace("/class $filename/", $property . "\n" . "class $filename", $content);
            file_put_contents($path, $content);
            $this->info('success file: ' . $path);
        }

    }
}
