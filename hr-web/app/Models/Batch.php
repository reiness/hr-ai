<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cv;
use App\Models\User;


class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cvs()
    {
        return $this->hasMany(Cv::class);
        
    }

    public function processedBatches()
    {
        return $this->hasMany(ProcessedBatch::class);
    }
}
