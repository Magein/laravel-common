<?php

namespace Magein\Common\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use magein\tools\common\Variable;

class MakeModelProperty extends Command
{
    /**
     *
     * Models目录下的所有文件，没有声明的都重新property
     * php artisan model:property
     *
     * Models\User.php的文件生成property
     * php artisan model:property user
     *
     * Models\Member\*.php的文件生成property,参数会转化为pascal格式
     * php artisan model:property --dir=member
     * php artisan model:property -D member
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:property {name?} {--D|--dir=}';

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $dir = $this->option('dir');

        if (empty($name)) {
            $this->error('请输入生成property的模型名称');
        }

        $name = Variable::instance()->pascal($name);

        if (!$dir) {
            $files = glob('./app/Models/*.php');
        } else {
            $dir = trim($dir, '/');
            $dir = Variable::instance()->pascal($dir);
            $files = glob("./app/Models/$dir/*.php");
        }

        $make = [];
        if ($files) {
            foreach ($files as $path) {
                $content = file_get_contents($path);
                $filename = pathinfo($path, PATHINFO_FILENAME);

                if ($filename === 'BaseModel') {
                    continue;
                }

                $namespace = preg_replace(['/\.\/app/', '/\.php/', '/\//'], ['App', '', '\\\\'], $path);
                if (preg_match('/\* @property/', $content) && empty($name)) {
                    continue;
                }

                if ($name && $filename != $name) {
                    continue;
                }

                $model = new $namespace();
                $attrs = Schema::getColumnListing($model->getTable());

                $property = '/**';
                $property .= "\n";

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
                    }
                }
                $property .= " */";

                // 替换属性
                $content = preg_replace("/class $filename/", $property . "\n" . "class $filename", $content);

                file_put_contents($path, $content);

                $this->info('success:' . $path);

                $make[] = $path;
            }
        }

        if (empty($make)) {
            $this->info('Empty ! You can enter the name  parameter to enforce');
            $this->info('or remove Models\wantmake.php property');
        }
    }
}
