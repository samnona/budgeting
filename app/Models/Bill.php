<?php

namespace App\Models;

use App\Traits\WithSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory, WithSearch;

    protected $guarded = [];

    protected $searchable = [
        'name'
    ];
}
