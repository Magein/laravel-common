<?php

namespace Magein\Common\Commands;

use Illuminate\Console\Command;

class MakeModelRequest extends Command
{
    protected $signature = 'mr {name?}';

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

    public function handle()
    {

    }
}