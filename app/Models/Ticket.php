<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Ticket extends Model
{
    protected $table = 'tickets';
    // use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_no',
        'is_drawn',
        'draw_id',
        'is_win',
        'user_id'
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
  
  
    public function user()
    {
      return $this->belongsTo('\App\Models\User', 'user_id');
    }

    public function draw()
    {
      return $this->belongsTo('\App\Models\Draw', 'draw_id');
    }
}
