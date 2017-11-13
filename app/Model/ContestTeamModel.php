<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/10/26
 * Time: 11:04
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContestTeamModel extends Model
{
    public $timestamps = false;
    protected $table = 'contest_team';
    protected $primaryKey = 't_id';
}