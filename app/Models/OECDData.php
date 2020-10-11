<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OECDData extends Model
{
    protected $table = "oecd_data";
    protected $fillable = [
        'Destination', 'Nationality', 'Value', 'Year'
    ];
    protected $Destination;
    protected $Nationality;
    protected $Value;
    protected $Year;

    public static function getMIGData($COU)
    {
        try {
            $data = self::from('oecd_data as data')
                ->select('Nationality', 'Destination', 'Value', 'Year')
                ->where('Destination', $COU)
                ->orWhere('Nationality', $COU)
                ->where('Value', ">", 0)
                ->orderby('Year', "Desc")
                ->get();
            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
