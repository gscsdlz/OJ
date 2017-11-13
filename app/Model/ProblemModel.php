<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProblemModel extends Model
{
    protected $table = 'problem';
    protected $primaryKey = 'pro_id';
    public $timestamps = false;
    private $ACNum = 0;
    private $AllNum = 0;
    private $ACFlag = 0;
    private $pointArray = array();

    public function submit()
    {
        return $this->hasMany('App\Model\StatusModel', 'pro_id');
    }

    public function points()
    {
        return $this->belongsToMany('App\Model\PointModel', 'point_pro', 'pro_id', 'point_id');
    }

    /**
     * 业务逻辑层（Business Logic Layer）
     */
    public function getACNum()
    {
        return $this->ACNum;
    }
    public function setACNum($num)
    {
        $this->ACNum = $num;
    }

    public function setAllNum($num)
    {
        $this->AllNum = $num;
    }
    public function getAllNum()
    {
        return $this->AllNum;
    }

    public function setACFlag($FlagA, $FlagB)
    {
        if ($FlagA > 0)
            $this->ACFlag = 1;
        else if ($FlagB > 0)
            $this->ACFlag = -1;
    }

    public function setPoint($pArr)
    {
        $this->pointArray = $pArr;
    }
    public function getACFlag()
    {
        return $this->ACFlag;
    }
    public function getPoints()
    {
        return $this->pointArray;
    }

    public function set_ac_status($userID)
    {
        $FlagA = $this->submit()->where([['status', 4],['contest_id', 0]])->where('user_id', $userID)->count();
        $FlagB = $this->submit()->where([['user_id' , $userID],['contest_id', 0]])->count();
        if ($FlagA > 0)
            $this->ACFlag = 1;
        else if ($FlagB > 0)
            $this->ACFlag = -1;
    }

    public function set_ac_num()
    {
        $this->AllNum = $this->submit()->where('contest_id', 0)->count();
        $this->ACNum = $this->submit()->where([['status', 4],['contest_id', 0]])->count();
    }

    public function set_points()
    {
        $lists = $this->points()->select('point_name')->get();
        foreach ($lists as $point){
            $this->pointArray[] = $point->point_name;
        }
    }
}