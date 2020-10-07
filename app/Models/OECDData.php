<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OECDData extends Model
{
    protected $table = "oecd_data";
    protected $Destination;
    protected $Nationality;
    protected $Value;
    protected $Year;

    public static function getMIGData($COU, $year) {
        try {
            $data = self::select('Nationality', 'Destination', 'Value')
                ->where('Destination', $COU)
                ->orWhere('Nationality', $COU)
                ->where('Year', $year)
                ->where('Value', ">", 0)
                ->get();
            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
