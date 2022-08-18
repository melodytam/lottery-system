<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Draw extends Model
{
    protected $table = 'draws';
    // use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    ];

    // /**
    //  * The attributes that should be hidden for serialization.
    //  *
    //  * @var array<int, string>
    //  */
    protected $date = [
        'created_at',
        'updated_at'
    ];
  
  
    public function ticket()
    {
      return $this->hasMany('\App\Models\Ticket');
    }
}
