<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/1/25
 * Time: 15:11
 */
class PageBuilder
{

    /**
     * @param $list
     * @return string
     */
    public static function toBuildPageListHtml($list)
    {
//        $html = '<tr><td colspan="5" class="text-center">列表为空</td></tr>';
        $html = '';
        if(!empty($list))
        {

            foreach($list as $v){
                $html.=self::toBuildPageItemHtml($v);
            }
        }

        return $html;
    }

    /**
     * item
     * @param $item
     * @return string
     */
    public static function toBuildPageItemHtml($item)
    {
        $info = json_encode($item);
        $id = $item['id'];
        $name = $item['name'];
        $img = $item['banner'];
        if($img)
        {
            $host = HolloEnv::getImgHost();
            $img = $host.$img;
            $img = "<img src='{$img}' class='table-img'>";
        }
        $type = $item['type'];
        $url = $item['type']['url'];
        if($type['status'] == 1)
        {
            $url = sprintf('%s%s',$url,$item['id']);
        }
        $html="
                <tr  data-id='{$id}'  data-info='{$info}'>
                    <td>{$img}</td>
                    <td>{$name}</td>
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



}