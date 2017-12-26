<?php
/**
 * Created by PhpStorm.
 * User: 南宫悟
 * Date: 2017/12/26
 * Time: 16:24
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class QQOAuthModel extends Model
{
    protected $table = "qq_oauth";
    protected $primaryKey = 'oauth_id';
    public $timestamps = false;
}