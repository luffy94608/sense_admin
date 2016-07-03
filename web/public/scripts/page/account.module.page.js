/**
 * Created by luffy on 15/7/16.
 */
$(document).ready(function(){


    var $modal=$('#editModal');
    var initModalEvent=function(){
        //添加项目
        $('.eui_del_item',$modal).unbind().wclick(function(){
            var num=$('.eui_item_list .eui_item').length;
            if(num<2)
            {
                $.showToast('就剩一个了',false);
                return false;
            }
            $(this).parents('.eui_item').remove();
        });
    };
    //添加项目
    $('#eui_add_item',$modal).unbind().wclick(function(){
        var item=$('.eui_item_template .eui_item',$modal).clone();
        $('.eui_item_list',$modal).append(item);
        initModalEvent();
    });

    $('.js_edit').unbind().bind('click',function(){
        var modal = $modal;
        var button = $(this); // Button that triggered the modal
        var edit=button.attr('data-edit');
        var title=modal.find('.modal-title');
        var id='',action='',name='',subArr='';
        if(edit==1){
            var info=JSON.parse(button.attr('data-info'));
            action=info.action;name=info.name;id=info.id;subArr=info.subArr;
            title.html('修改权限');
        }else{
            title.html('添加权限');
        }

        var clearModalData=function(){
            modal.find('#eui-action').val('');
            modal.find('#eui-name').val('');
            var itemTemplate=$('.eui_item_template .eui_item',$modal).clone();
            $('.eui_item_list',$modal).html(itemTemplate);
        };
        var initModalData=function(){
            modal.find('#eui-action').val(action);
            modal.find('#eui-name').val(name);
            if(subArr && subArr.length){
                $('.eui_item_list',$modal).html('');
                for(var i=0;i<subArr.length;i++){
                    var itemTemplate=$('.eui_item_template .eui_item',$modal).clone();
                    itemTemplate.find('.eui_item_action').attr('data-id',subArr[i].id);
                    itemTemplate.find('.eui_item_action').val(subArr[i].action);
                    itemTemplate.find('.eui_item_name').val(subArr[i].name);
                    $('.eui_item_list',$modal).append(itemTemplate);
                }
            }
        };
        clearModalData();
        initModalData();
        initModalEvent();
        var submitBtn=modal.find('#eui-confirm');
        $modal.modal();
        submitBtn.unbind().wclick(function(){
            var data={
                id:id,
                action:modal.find('#eui-action').val(),
                name:modal.find('#eui-name').val()
            };
            var aLength=data.action.length;
            if(aLength<1 || aLength>30){
                $.showToast('action长度为1-30个字符',false);
                return false;
            }
            var pLength=data.name.length;
            if(pLength<1 || pLength>30){
                $.showToast('模块名长度为1-30个字符',false);
                return false;
            }
            var subArr=new Array;
            var listNode=modal.find('.eui_item_list .eui_item');
            if(listNode && listNode.length){
                for(var i=0;i<listNode.length;i++){
                    var tmpAction=$('.eui_item_action',$(listNode[i]))[0];
                    var tmpName=$('.eui_item_name',$(listNode[i]))[0];
                    var subId=$.trim($(tmpAction).attr('data-id'));
                    var subAction=$.trim($(tmpAction).val());
                    var subName=$.trim($(tmpName).val());
                    if(subAction && subName){
                        var subLength=subAction.length;
                        if(subLength<1 || subLength>30){
                            $.showToast('子action长度为1-30个字符',false);
                            return false;
                        }
                        var subLength=subName.length;
                        if(subLength<1 || subLength>30){
                            $.showToast('子模块名长度为1-30个字符',false);
                            return false;
                        }
                        subArr.push({id:subId,action:subAction,name:subName});
                    }
                }
            }
            if(subArr.length<1){
                $.showToast('请至少添加一个子模块',false);
                return false;
            }
            data.subArr=subArr;
            $.wpost('/account/update-privilege-ajax',data,function(res){
                $.showToast('添加成功',true);
                modal.modal('hide');
                if(id){
                    var tds=button.parents('tr').children('td');
                    button.attr('data-info',JSON.stringify(res));
                    var html='';
                    if(subArr && subArr.length){
                        for(var i=0;i<subArr.length;i++) {
                            html+="<span class='btn label label-info'>"+subArr[i].name+"</span>";
                        }
                    }
                    $(tds[0]).html(data.action);
                    $(tds[1]).html(data.name);
                    $(tds[2]).html(html);
                }else{
                    location.reload();
                }

            });
        });
    });

    $('.js_delete').unbind().wclick(function(){
        var $this=$(this);
        var id=$this.attr('data-id');
        $.confirm({content:'确认删除吗',success:function(){
            if(!id) {
                $.showToast('非法操作',false);
                return false;
            }
            $.wpost('/account/delete-ajax',{id:id,type:3},function(){
                $.showToast('删除成功',true);
                $this.parents('tr').remove();
            });
        }});

    });
});