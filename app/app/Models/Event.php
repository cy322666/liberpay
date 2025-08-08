<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'responsible_id',
        'responsible_id_tech',
        'event',
        'company_id',
        'contact_id',
        'status',
    ];
}
