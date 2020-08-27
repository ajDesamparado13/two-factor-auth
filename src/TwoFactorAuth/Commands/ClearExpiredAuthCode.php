<?php

namespace Freedom\TwoFactorAuth\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class ClearExpiredAuthCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth-code:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Auth Code';

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
     * @return mixed
     */
    public function handle()
    {
        $model = app()->make(\Config::get('two-factor-auth.model'));
        $model->where('expire_on','<=',Carbon::now())->orWhere('disabled',1)->delete();
    }
}
