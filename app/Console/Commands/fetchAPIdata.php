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
    protected $description = 'APIデータ取得（テスト用）';

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

        foreach ($countryList as $country) {
            $COU = $country['code'];
            $oecd->fetchAPIDATA($COU);

            echo $country['Name'] . "のデータ更新が完了しました" . "\n";
        }
    }
}
