<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shopper\Shopper;
use App\Models\Shopper\Status;

class CheckActiveShoppers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Check:ActiveShoppers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * Automatically find the active shoppers for more than 2hrs and mark them as "completed"
     *
     * @return int
     */
    public function handle()
    {
        $lastDate = date("Y-m-d H:i:s", time() - 2 * 60 * 60);
        Shopper::where('check_in', '<=', $lastDate)->update(['status_id' => Status::getIdByName('Completed')]);
        $this->info("Shoppers checked");
        
        return Command::SUCCESS;
    }
}
