<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/26
 * Time: 9:52
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ContestProblemModel extends Model
{
    protected $primaryKey = 'p_id'; //primary id
    protected $table = 'contest_pro';
    public $timestamps = false;


    private $ACNum = 0;
    private $AllNum = 0;
    private $ACFlag = 0;

    public function submit()
    {
        return $this->hasMany('App\Model\StatusModel', 'pro_id');
    }

    /**
     * 业务逻辑层（Business Logic Layer）
     */
    public function getACNum()
    {
        return $this->ACNum;
    }
    public function getAllNum()
    {
        return $this->AllNum;
    }
    public function getACFlag()
    {
        return $this->ACFlag;
    }


    public function set_ac_status($userID, $cid)
    {
          $FlagA = StatusModel::where([
              ['status', 4],
              ['user_id', $userID],
              ['contest_id', $cid],
              ['pro_id', $this->pro_id]
          ])->count();
          $FlagB = StatusModel::where([
              ['user_id' , $userID],
              ['contest_id', $cid],
              ['pro_id', $this->pro_id]
          ])->count();
          if ($FlagA > 0)
              $this->ACFlag = 1;
          else if ($FlagB > 0)
              $this->ACFlag = -1;
    }

    public function set_ac_num($cid = 0)
    {
        $this->ACNum = StatusModel::where([
            ['status', 4],
            ['contest_id', $cid],
            ['pro_id', $this->pro_id]
        ])->count();
        $this->AllNum = StatusModel::where([
            ['contest_id', $cid],
            ['pro_id', $this->pro_id]])->count();

    }

}