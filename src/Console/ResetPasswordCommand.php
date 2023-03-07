<?php
namespace CherryneChou\Admin\Console;

use Illuminate\Console\Command;

class ResetPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password for a specific admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {

    }
}