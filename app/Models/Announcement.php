<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory;
    protected $fillable =[
        'title',
        'description',
        'category_id',
        'operation_type',
        'price',
        'is_completed',
        'is_cancelled',
        'exchange_location_address',
        'exchange_location_lng',
        'exchange_location_lat',
        'created_by',

    ];
    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function category():BelongsTo{
        return $this->belongsTo(Category::class);
    }

    public function favorites(){
        return $this->hasMany(Favorite::class);
    }

}
