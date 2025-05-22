<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable = [
        'created_by',
        'updated_by',
        'announcement_id'
    ];

    public function user(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function announcement(){
        return $this->belongsTo(Announcement::class);
    }
}
