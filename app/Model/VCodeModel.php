<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/8/3
 * Time: 10:47
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class VCodeModel extends Model
{
    protected $table = 'vcode';
    protected $primaryKey = 'vid';
    public $timestamps = false;
}