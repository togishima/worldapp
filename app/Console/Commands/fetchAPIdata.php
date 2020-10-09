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
    protected $signature = 'command:name';

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
     *
     * @return int
     */
    public function handle()
    {
        $oecd = new OECD;
        $data = $oecd->getInBoundData("JPN", 2012);
        //各データをOECDDataオブジェクトにマッピングしてMySQLに保存
        foreach ($data as $c_name => $nat) {
            $dataModel = new OECDData;
            $dataModel->Destination = $c_name;
            foreach ($nat as $nat => $obsv) {
                $dataModel->Nationality = $nat;
                foreach ($obsv as $year => $value) {
                    $dataModel->Value = $value;
                    $dataModel->Year = $year;
                    $dataModel->save();
                }
            }
        }
    }
}
