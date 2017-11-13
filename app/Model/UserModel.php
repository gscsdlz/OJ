<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/7/31
 * Time: 17:52
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $primaryKey = 'user_id';
    protected $table = 'users';
    public $timestamps = false;

    public function team()
    {
        return $this->belongsTo('App\Model\TeamModel', 'team_id', 'team_id');
    }
}