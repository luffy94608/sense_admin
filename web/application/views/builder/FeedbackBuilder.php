<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/1/25
 * Time: 15:11
 */
class FeedbackBuilder
{

    /**
     * @param $list
     * @return string
     */
    public static function toBuildFeedbackListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {

            foreach($list as $v){
                $html.=self::toBuildFeedbackItemHtml($v);
            }
        }

        return $html;
    }

    /**
     * item
     * @param $item
     * @return string
     */
    public static function toBuildFeedbackItemHtml($item)
    {
        $info = json_encode($item);
        $id = $item['id'];
        $name = $item['name'];
        $email = $item['email'];
        $content = $item['content'];
        $time = $item['created_at'];
        $html="
                <tr  data-id='{$id}'  data-info='{$info}'>
                    <td>{$name}</td>
                    <td>{$email}</td>
                    <td>{$time}</td>
                    <td>
                        <a href='javascript:;' class='btn default btn-sm blue js_detail'>
                            查看内容
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
    public static function toBuildApplyListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {

            foreach($list as $v){
                $html.=self::toBuildApplyItemHtml($v);
            }
        }

        return $html;
    }

    /**
     * item
     * @param $item
     * @return string
     */
    public static function toBuildApplyItemHtml($item)
    {
        $info = json_encode($item);
        $id = $item['id'];
        $name = $item['name'];
        $email = $item['email'];
        $mobile = $item['mobile'];
        $company = $item['company'];
        $commodity = $item['commodity'];
        $type = $item['type'];
        $desc = $item['desc'];
        $time = $item['created_at'];
        $html="
                <tr  data-id='{$id}'  data-info='{$info}'>
                    <td>{$name}</td>
                    <td>{$email}</td>
                    <td>{$mobile}</td>
                    <td>{$company}</td>
                    <td>{$commodity}</td>
                    <td>{$type}</td>
                    <td>{$time}</td>
                    <td>
                        <a href='javascript:;' class='btn default btn-sm blue js_detail'>
                            查看内容
                        </a>
                    </td>
                </tr>
            ";
        return $html;
    }



}