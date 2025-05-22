<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use  HasFactory;
    protected $fillable=[
        'name',
        'description'
    ];

    public function announcements(){
        return $this->hasMany(Announcement::class);
    }

    public function preferences(){
        return $this->belongsToMany(User::class,'preferences','category_id','created_by');
    }
}
