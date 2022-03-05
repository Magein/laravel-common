<?php

namespace Magein\Common\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use magein\tools\common\Variable;

class MakeModel extends Command
{
    /**
     *
     * 默认继承BaseModel  --ng 表示不创建二级目录  not group
     *
     * php artsion model:create member_auth
     *
     * @var string
     */
    protected $signature = 'model:create {name?} {--ng}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建模型类 当使用下划线的时候，会创建二级目录 --ng 不创建二级目录';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');
        $group = $this->option('ng');

        $dir = '';
        $namespace = 'namespace App\Models';
        if (!$group) {
            $params = explode('_', $name);
            $dir = $params[0];
        }

        if ($dir) {
            $path = './app/Models/' . Variable::instance()->pascal($dir);
            if (!is_dir($path)) {
                mkdir($path, 757);
            }
            $namespace .= '\\' . Variable::instance()->pascal($dir);
        } else {
            $path = './app/Models';
        }

        $class_name = Variable::instance()->pascal($name);
        $filename = $path . '/' . $class_name . '.php';

        if (preg_match('/y$/', $name)) {
            $attrs = Schema::getColumnListing(preg_replace('/y$/', 'ies', $name));
        } elseif (!preg_match('/s$/', $name)) {
            $attrs = Schema::getColumnListing($name . 's');
        } else {
            $attrs = Schema::getColumnListing($name);
        }

        $fillable = "[\n";
        if ($attrs) {
            foreach ($attrs as $attr) {
                if (in_array($attr, ['id', 'money', 'balance', 'score', 'integral', 'created_at', 'updated_at', 'deleted_at'])) {
                    continue;
                }
                $fillable .= "      '$attr',\n";
            }
        }
        $fillable .= "]";

        $call = function () use ($name, $dir) {
            $this->call('mp', ['name' => $name, '--d' => $dir]);
        };
        if (is_file($filename)) {
            $this->info('exist:' . $filename);
            $call();
            exit();
        }

        $content = <<<EOF
<?php

$namespace;

use App\Models\BaseModel;

class {$class_name} extends BaseModel
{
    protected \$fillable = $fillable;
}
EOF;

        file_put_contents($filename, $content);

        $this->info('make successful');

        $call();
    }
}
