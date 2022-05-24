<?php


namespace Magein\Common\Commands;

use Illuminate\Console\Command;

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
    protected $signature = 'model:validate {name}';

    protected $description = '创建模型的验证类';

    private function help()
    {
        $this->comment('说明：');
        $this->info('   为否允许为空判断依据：是否有默认值 有默认值则可以为空');
        $this->info('   类型判断依据：int、tinyint、类型对应int类型 varchar、char、text对应string类型 date类型对应date类型');
        $this->info('   长度验证：string类型的会验证长度(非中文验证)');
    }
}