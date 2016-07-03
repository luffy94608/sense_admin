<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/1/25
 * Time: 15:11
 */
class BannerBuilder
{
    /**
     * 生成 list html
     * @param $list
     * @return string
     */
    public static function toBuildListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {
            foreach($list as $v){
                $html.=BannerBuilder::toBuildItem($v);
            }
        }

        return $html;
    }

    /**
     *生成 list item
     * @param $item
     * @return string
     */
    public static function toBuildItem($item)
    {
        $html = '';

        $info=json_encode($item);
        $id = $item['id'];
        $title = $item['title'];
        $desc = $item['sub_title'];
        $url = $item['url'];
        $btnName = $item['btn_name'];
        $btnUrl = $item['btn_url'];
        $img = $item['img'];
        if($img)
        {
            $config = Yaf_Registry::get('config');
            $host = $config->img->host;
            $img = $host.$img;
            $img = "<img src='{$img}' width='140' height='70'>";
        }

        $html.="
                <tr  data-info='{$info}' data-id='{$id}'>
                    <td>{$title}</td>
                    <td>{$desc}</td>
                    <td>{$img}</td>
                    <td>{$btnName}</td>
                    <td>{$btnUrl}</td>
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
     * 生成 list html
     * @param $list
     * @return string
     */
    public static function toBuildPartnerListHtml($list)
    {
        $html = '';
        if(!empty($list))
        {
            foreach($list as $v){
                $html.=BannerBuilder::toBuildPartnerItem($v);
            }
        }

        return $html;
    }

    /**
     *生成 list item
     * @param $item
     * @return string
     */
    public static function toBuildPartnerItem($item)
    {
        $html = '';

        $info=json_encode($item);
        $id = $item['id'];
        $url = $item['url'];
        $logo = $item['logo'];
        if($logo)
        {
            $config = Yaf_Registry::get('config');
            $host = $config->img->host;
            $logo = $host.$logo;
            $logo = "<img src='{$logo}' width='140' height='70'>";
        }

        $html.="
                <tr  data-info='{$info}' data-id='{$id}'>
                    <td>{$logo}</td>
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