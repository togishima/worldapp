<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OECDData extends Model
{
    protected $table = "oecd_data";
    protected $Destination;
    protected $Nationality;
    protected $Value;
    protected $Year;
}
