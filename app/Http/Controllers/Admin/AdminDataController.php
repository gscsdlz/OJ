<?php
/**
 * Created by PhpStorm.
 * User: 南宫悟
 * Date: 2017/11/11
 * Time: 12:10
 * 该类负责管理所有的题目数据文件，但是不接收路由调用，只接收控制器作为独立类调用。
 * 不需要实例化
 */

namespace App\Http\Controllers\Admin;


class AdminDataController
{

    static $rootPath = '/home/judge/data'; //数据根目录
    static $tmpPath = '/home/judge/data/tmp'; //数据文件缓存目录

    public static function get_list($id)
    {
        $id = (int)$id;
        if ($id >= 1000 && $id <= 9999) {
            $handle = self::open_dir(self::$rootPath . '/' . $id);
            chdir(self::$rootPath . '/' . $id);
            $fileInfos = [];
            while (false !== ($file = readdir($handle))) {
                if (!is_dir($file)) {
                    $fileInfos[] = [
                        'filename' => basename($file),
                        'size' => filesize($file).' Byte',
                        'mtime' => date('Y-m-d H:i:s', filemtime($file)),
                    ];
                }
            }
            uasort($fileInfos, function ($a, $b) {
                return $a['filename'] < $b['filename'];
            });
            closedir($handle);
            return $fileInfos;
        } else
            return false;
    }

    public static function del_file($id, $filename)
    {
        $path = self::$rootPath.'/'.$id.'/'.$filename;
        if(file_exists($path)) {
            $npath = self::$tmpPath.'/'.$id.'_'.time().'_'.$filename;
            if(rename($path, $npath))
                return $npath;
        }
        return '';
    }

    public static function undo($filename)
    {
        if(file_exists($filename)) {
            $filesize = filesize($filename).' Byte';
            $mtime = date('Y-m-d H:i:s', filemtime($filename));
            $arr = explode('/', $filename);
            $arr = explode('_', $arr[count($arr) - 1]);
            $pid = $arr[0];
            $name = $arr[2];

            if(rename($filename, self::$rootPath.'/'.$pid.'/'.$name))
                return [$name, $filesize, $mtime];
        }
        return false;
    }

    /**
     * @param $id
     * @param $name
     * 批量上传支持，用户上传zip 服务端自解压即可。
     * @return mixed
     */
    public static function upload_files($id, $name)
    {
        //.in .out不需要做任何处理
        if(strpos($name, '.in') !== false || strpos($name,'.out') !== false) {
            return true;
        }
        $path = self::$rootPath.'/'.$id;
        if(file_exists($path) && strpos($name,'.zip') !== false) {
            chdir($path);
            system('unzip -q '.$name);
            system('mv '.$name.' /tmp');
            return true;
        }
        return false;
    }

    /**
     * @param $id
     * 批量下载zip题目数据
     * @return string
     */
    public static function zip_files($id)
    {
        $path = self::$rootPath.'/'.$id;
        if(file_exists($path) && is_dir($path)) {
            chdir($path);
            system('zip -r -q '.$id.'.zip ./');
            $path = $path.'/'.$id.'.zip';

            if(file_exists($path)) {
                rename($path, self::$tmpPath.'/'.$id.'.zip');
                return self::$tmpPath.'/'.$id.'.zip';
;
            }
        }
        return '';
    }

    private static function open_dir($path)
    {
        if(!file_exists($path)) {
            system("mkdir -p ".$path);
        }
        return opendir($path);
    }
}