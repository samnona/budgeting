<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appropriation extends Model
{
    use HasFactory;

    protected $guarded = [];


    public static function booted()
    {
        self::created(function (Appropriation $appropriation) {

            ObjectExpenditure::query()
                ->where('id', $appropriation->object_expenditure_id)
                ->decrement('balance', $appropriation->expense);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function objectExpenditure()
    {
        return $this->belongsTo(ObjectExpenditure::class);
    }
}
