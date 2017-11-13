<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/26
 * Time: 16:47
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class QuestionModel extends Model
{
    protected $table = 'question';
    protected $primaryKey ='question_id';

    public $timestamps = false;
}