<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/1/25
 * Time: 15:11
 */
class ManageBuilder
{
    /**
     * 生成 list html
     * @param $list
     * @return string
     */
    public static function toBuildRecruitListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {
            foreach($list as $v){
                $html.=self::toBuildRecruitItem($v);
            }
        }

        return $html;
    }

    /**
     *生成招聘 list item
     * @param $item
     * @return string
     */
    public static function toBuildRecruitItem($item)
    {
        $html = '';
        $info=json_encode($item);
        $id = $item['id'];
        $title = $item['title'];
        $location = $item['location'];
        $num = $item['num'];
        $experience = $item['experience'];

        $degree = $item['degree'];
        $nature = $item['nature'];
        $salary = $item['salary'];
        $time = $item['updated_at'];

        $html.="
                <tr  data-info='{$info}' data-id='{$id}'>
                    <td>{$title}</td>
                    <td>{$location}</td>
                    <td>{$num}</td>
                    <td>{$experience}</td>
                    <td>{$degree}</td>
                    <td>{$nature}</td>
                    <td>{$salary}</td>
                    <td>{$time}</td>
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
     * 生成新闻 list html
     * @param $list
     * @return string
     */
    public static function toBuildNewsListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {
            foreach($list as $v){
                $html.=self::toBuildNewsItem($v);
            }
        }

        return $html;
    }

    /**
     *生成新闻 list item
     * @param $item
     * @return string
     */
    public static function toBuildNewsItem($item)
    {
        $html = '';
        $info=json_encode($item);
        $id = $item['id'];
        $title = $item['title'];
        $time = $item['time'];

        $html.="
                <tr  data-info='{$info}' data-id='{$id}'>
                    <td>{$title}</td>
                    <td>{$time}</td>
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