<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OECDData extends Model
{
    protected $table = "oecd_data";
    protected $from;
    protected $to;
    protected $value;
    protected $year;
}
