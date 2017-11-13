<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/26
 * Time: 16:47
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class AnswerModel extends Model
{
    protected $table = 'answer';
    protected $primaryKey ='answer_id';

    public $timestamps = false;
}