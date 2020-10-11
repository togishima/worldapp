<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\OECD;
use App\Models\OECDData;

class fetchAPIdata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fetchAPIdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DBの国ごとのレコードをAPIデータから更新するバッチ';

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
        $oecd = new OECD;
        $countryList = $oecd->getCountryList();

        foreach ($countryList as $COU) {
            $oecd->fetchAPIDATA($COU[0]);

            echo $COU . "のデータ更新が完了しました" . "\n";
        }
    }
}
