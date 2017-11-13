<?php
namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class StatusModel extends Model
{
    protected $table = 'status';
    protected $primaryKey = 'submit_id';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Model\UserModel', 'user_id');
    }
}