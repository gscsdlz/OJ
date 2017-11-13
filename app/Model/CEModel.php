<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/1
 * Time: 9:24
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CEModel extends Model
{
    protected $table = 'ce_info';
    protected $primaryKey = 'submit_id';
    public $timestamps = false;

    public function submit()
    {
        return $this->hasOne('App\Model\StatusModel', 'submit_id');
    }
}