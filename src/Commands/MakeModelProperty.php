<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use magein\tools\common\Variable;

class MakeModelProperty extends Command
{
    /**
     *
     * php artisan make:command ModelProperty
     *
     * Models目录下的所有文件，没有声明的都重新property
     * php artisan mp
     * 为Models\User.php的文件生成property
     * php artisan mp:user
     * 为Models\Member\*.php的文件生成property
     * php artisan --d=member（会转化为pascal格式）
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mp {name?} {--d=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Models目录下的模型类类都追加上"@property 属性名称"';

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

        $name = Variable::instance()->pascal($name);

        $dir = $this->option('d');
        if (is_null($dir)) {
            $files = glob('./app/Models/*.php');
        } else {
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
