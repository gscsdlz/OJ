<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class PointModel extends Model
{
    protected $table = 'point';
    protected $primaryKey = 'point_id';
    public $timestamps = false;
}