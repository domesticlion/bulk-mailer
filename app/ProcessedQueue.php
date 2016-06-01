<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcessedQueue extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'processed_queues';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['queue_id'];


}
