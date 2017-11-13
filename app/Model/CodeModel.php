<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/8/1
 * Time: 8:20
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class CodeModel extends Model
{
    protected $table = 'codes';
    protected $primaryKey = 'submit_id';

    public function submit()
    {
        return $this->hasOne('App\Model\StatusModel', 'submit_id');
    }
}