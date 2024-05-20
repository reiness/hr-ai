<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessedBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'name',
        'user_input',
        'status',
        'result',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
