<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/10/28
 * Time: 21:24
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class PointProblemModel extends Model
{
    protected $table = 'point_pro';
    protected $primaryKey = 'id';
    public $timestamps = false;
}