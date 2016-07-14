<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/1/25
 * Time: 15:11
 */
class HomeBuilder
{

    /**
     * @param $list
     * @return string
     */
    public static function toBuildListHtml($list)
    {
//        $html = '<tr><td colspan="5" class="text-center">列表为空</td></tr>';
        $html = '';
        if(!empty($list))
        {

            foreach($list as $v){
                $html.=self::toBuildItemHtml($v);
            }
        }

        return $html;
    }

    /**
     * item
     * @param $item
     * @return string
     */
    public static function toBuildItemHtml($item)
    {
        $info = json_encode($item);
        $id = $item['id'];
        $title = $item['title'];
        $sub_title = $item['sub_title'];
        $pic = $item['pic'];
        $host = HolloEnv::getImgHost();

        $img = "<img src='{$host}{$pic}' class='table-img'>";

        $html="
                <tr  data-id='{$id}'  data-info='{$info}'>
                    <td>{$img}</td>
                    <td>{$title}</td>
                    <td>{$sub_title}</td>
                    <td>
                         <a href=\"javascript:;\" class=\"btn default green js_up\">
                            <i class=\"fa fa-arrow-up\"></i>
                        </a>
                        <a href=\"javascript:;\" class=\"btn default  js_down\">
                            <i class=\"fa fa-arrow-down\"></i>
                        </a>
                    </td>
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