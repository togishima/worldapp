<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OECDData extends Model
{
    protected $table = "oecdData";
    protected $fillable = [
        'Destination', 'Nationality', 'Value', 'Year'
    ];
    protected $Destination;
    protected $Nationality;
    protected $Value;
    protected $Year;

    public static function getMIGData($CO)
    {
        try {
            $data = self::from('oecdData')
                ->select('Nationality', 'Destination', 'Value', 'Year')
                ->where('Destination', $CO)
                ->orWhere('Nationality', $CO)
                ->where('Value', ">", 0)
                ->orderby('Year', "Desc")
                ->get();
            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
