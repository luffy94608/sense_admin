<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/1/25
 * Time: 15:11
 */
class LockBuilder
{

    /**
     * @param $list
     * @return string
     */
    public static function toBuildLockTypeListHtml($list)
    {
//        $html = '<tr><td colspan="5" class="text-center">列表为空</td></tr>';
        $html = '';
        if(!empty($list))
        {

            foreach($list as $v){
                $html.=LockBuilder::toBuildLockTypeItemHtml($v);
            }
        }

        return $html;
    }

    /**
     * item
     * @param $item
     * @return string
     */
    public static function toBuildLockTypeItemHtml($item)
    {
        $info = json_encode($item);
        $id = $item['id'];
        $name = $item['name'];
        $title = $item['title'];
        $img = $item['img'];
        if($img)
        {
            $host = HolloEnv::getImgHost();
            $img = $host.$img;
            $img = "<img src='{$img}' class='table-img'>";
        }
        $html="
                <tr  data-id='{$id}'  data-info='{$info}'>
                    <td>{$img}</td>
                    <td>{$name}</td>
                    <td>{$title}</td>
                    <td>
                        <a href='javascript:;' class='btn default btn-sm blue js_edit'>
                            <i class='fa fa-edit'></i> 
                            编辑
                        </a>
                        <a href='javascript:;' class='btn default btn-sm red js_delete'>
                            <i class='fa fa-trash-o'></i> 
                            删除
                        </a>
                    </td>
                </tr>
            ";
        return $html;
    }


    /**
     * 生成下载 list html
     * @param $list
     * @return string
     */
    public static function toBuildDownloadListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {
            foreach($list as $v){
                $html.=self::toBuildDownloadItem($v);
            }
        }

        return $html;
    }

    /**
     *生成下载 list item
     * @param $item
     * @return string
     */
    public static function toBuildDownloadItem($item)
    {
        $html = '';
        $info=json_encode($item);
        $id = $item['id'];
        $title = $item['title'];
        $url = $item['url'];
        $btn_name = $item['btn_name'];
        $type = $item['type'] ? $item['type']['name'] : '其他';

        if(stripos($url,'http://')===false && stripos($url,'https://')===false && stripos($url,'ftp://')===false){
            $host = HolloEnv::getImgHost();
            $url = $host.$url;
        }

        $html.="
                <tr  data-info='{$info}' data-id='{$id}'>
                    <td>{$title}</td>
                    <td>{$type}</td>
                    <td>{$btn_name}</td>
                    <td>{$url}</td>
                    <td>
                        <a href='javascript:;' class='btn default btn-sm blue js_edit'>
                            <i class='fa fa-edit'></i> 
                            编辑 
                        </a>
                       <a href='javascript:;' class='btn default btn-sm red js_delete'>
                            <i class='fa fa-trash-o'></i> 
                            删除 
                        </a>
                    </td>
                </tr>
            ";

        return $html;
    }



    /**
     * @param $list
     * @return string
     */
    public static function toBuildLockListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {
            foreach($list as $v){
                $html.=LockBuilder::toBuildLockItemHtml($v);
            }
        }

        return $html;
    }

    /**
     * item
     * @param $item
     * @return string
     */
    public static function toBuildLockItemHtml($item)
    {
        $info = json_encode($item);
        $id = $item['id'];
        $version = $item['version'];
        $type = $item['type']['name'];
        $pic = $item['pic'];

        if(stripos($pic,'http://')===false && stripos($pic,'https://')===false && stripos($pic,'ftp://')===false){
            $host = HolloEnv::getImgHost();
            $pic = $host.$pic;
            $pic = "<img src='{$pic}' class='table-img'>";
        }
        $html="
                <tr  data-id='{$id}'  data-info='{$info}'>
                    <td>{$pic}</td>
                    <td>{$type}</td>
                    <td>{$version}</td>
                    <td>
                        <a href='javascript:;' class='btn default btn-sm blue js_edit'>
                            <i class='fa fa-edit'></i> 
                            编辑
                        </a>
                        <a href='javascript:;' class='btn default btn-sm red js_delete'>
                            <i class='fa fa-trash-o'></i> 
                            删除
                        </a>
                    </td>
                </tr>
            ";
        return $html;
    }

}