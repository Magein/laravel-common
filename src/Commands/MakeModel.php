<?php

namespace Magein\Common\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use magein\tools\common\Variable;

class MakeModel extends Command
{
    /**
     * 创建的模型会默认继承 BaseModel 可以使用 --extend=laravel
     *
     * 下面命令会创建Models/Member/MemberAuth.php
     * php artsion model:create member_auth
     *
     * 下面命令会创建Models/MemberAuth.php
     * php artsion model:create member_auth --ignore
     *
     * 下面命令会创建Models/MemberAuth.php并且继承laravel的model
     * php artsion model:create member_auth --ignore --extend=laravel
     * php artsion model:create member_auth --ignore -E laravel
     *
     *
     * @var string
     */
    protected $signature = 'model:create {name?} {--ignore} {--E|extend=}';

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

    private function help()
    {
        $this->comment('请参考以下示列');
        $this->info('   创建Models/Member/MemberAuth.php                  php artsion model:create member_auth');
        $this->info('   创建Models/MemberAuth.php                         php artsion model:create member_auth --ignore');
        $this->info('   创建Models/MemberAuth.php并且继承laravel的model   php artsion model:create member_auth --ignore --extend=laravel');
        $this->info('   创建Models/MemberAuth.php并且继承laravel的model   php artsion model:create member_auth --ignore -E laravel');
    }

    public function handle()
    {
        $name = $this->argument('name');
        $ignore = $this->option('ignore');
        $extend = $this->option('extend');

        if (empty($name)) {
            $this->error('请输入要创建的表名称');
            $this->help();
            exit(1);
        }

        $dir = '';
        $namespace = 'namespace App\Models';
        if (!$ignore) {
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

        if (empty($attrs)) {
            $this->error('没有检测到表字段信息，请检查表名称');
            $this->info('请注意：');
            $this->info('   y结尾会转化成ies，不');
            $this->info('   不是以s结果的会追加上s');
            exit(1);
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

        $call = function () use ($name) {
            $this->call('model:property', ['name' => $name]);
        };

        if (is_file($filename)) {
            $this->error('file exists:' . $filename);
            $call();
            exit();
        }

        $extends = 'BaseModel';
        $extends_use = 'use App\Models\BaseModel;';

        if ($extend === 'laravel') {
            $extends = 'Model';
            $extends_use = 'use Illuminate\Database\Eloquent\Model;';
        }

        $content = <<<EOF
<?php

$namespace;

$extends_use

class {$class_name} extends $extends
{
    protected \$fillable = $fillable;
}
EOF;

        file_put_contents($filename, $content);

        $this->info('make model successful');

        $call();
    }
}
