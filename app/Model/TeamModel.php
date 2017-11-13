<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/8/2
 * Time: 15:07
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class TeamModel extends Model
{
    protected $table = 'team';
    protected $primaryKey = 'team_id';
    public $timestamps = false;
}