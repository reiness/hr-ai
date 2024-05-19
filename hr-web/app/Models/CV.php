<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = ['batch_id', 'file_path'];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
