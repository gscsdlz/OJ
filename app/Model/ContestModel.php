<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/1
 * Time: 15:13
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContestModel extends Model
{
    protected $table = 'contest';
    protected $primaryKey = 'contest_id';

    public $timestamps = false;


    public function problems()
    {
        return $this->hasMany('App\Model\ContestProblemModel', 'contest_id');
    }
}