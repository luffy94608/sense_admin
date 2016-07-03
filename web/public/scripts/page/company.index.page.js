/**
 * xiufei.lu
 */

$(document).ready(function($){
    var $modal=$('#editModal');
    $('.js_edit').unbind().bind('click',function(){
        var modal = $modal;
        var button = $(this); // Button that triggered the modal
        var edit=button.attr('data-edit');
        var title=modal.find('.modal-title');
        var id='',privileges='',name='',domain='';
        if(edit==1){
            var info=JSON.parse(button.attr('data-info'));
            id=info.id;
            name=info.name;
            domain=info.domain;
            privileges=info.privileges;

            title.html('修改企业信息');
        }else{
            title.html('添加企业信息');
        }
        var initCheckbox=function(privileges){
            var cObjs=$('input',modal.find('#eui-privilege'));
            $(cObjs).prop('checked',false);

            if(privileges && privileges.length){
                var pids=[];
                for(var i=0;i<privileges.length;i++){
                    if(privileges[i]){
                        var pid=privileges[i].id;
                        pids.push(pid);
                    }
                }

                if(cObjs && cObjs.length){
                    for(var i=0;i<cObjs.length;i++){
                        var cid=$(cObjs[i]).val();
                        if($.inArray(cid,pids)!=-1){
                            $(cObjs[i]).prop('checked',true);
                        }
                    }
                }
            }
        };
        initCheckbox(privileges);
        modal.find('#eui-name').val(name);
        modal.find('#eui-domain').val(domain);
        var submitBtn=modal.find('#eui-confirm');
        submitBtn.unbind().wclick(function(){

            var pData=$('input:checked',modal.find('#eui-privilege'));
            var pArr=[];
            var pResult=[];
            for(var i=0;i< pData.length;i++)
            {
                var pid=$.trim($(pData[i]).val());
                var pname=$.trim($(pData[i]).attr('data-name'));
                pArr.push(pid);
                pResult.push({id:pid,name:pname});
            }
            var data={
                id:id,
                name:$.trim(modal.find('#eui-name').val()),
                domain: $.trim(modal.find('#eui-domain').val()),
                privilege:pArr
            };
            var aLength=data.name.length;
            if(aLength<1 || aLength>30){
                $.showToast('角色为1-50个字符',false);
                return false;
            }
            $.wpost('/company/update-company-ajax',data,function(res){
                $.showToast('添加成功',true);
                modal.modal('hide');
                if(id){
                    var tds=button.parents('tr').children('td');
                    var srcPdata=JSON.parse(button.attr('data-info'));
                    srcPdata.name=data.name;
                    srcPdata.domain=data.domain;
                    var pStr='';
                    if(pResult && pResult.length){
                        srcPdata.privileges=pResult;
                        for(var i=0;i<pResult.length;i++){
                            pStr+=' <span  class="label label-primary">'+pResult[i].name+'</span>';
                        }
                        $(tds[2]).html(pStr);
                    }

                    button.attr('data-info',JSON.stringify(srcPdata));
                    $(tds[0]).html(data.name);
                    $(tds[1]).html(data.domain);

                }else{
                    location.reload();
                }

            });
        });
        $modal.modal();
    });

    $('.js_delete').unbind().wclick(function(){
        var $this=$(this);
        var id=$this.attr('data-id');
        $.confirm({content:'确认删除吗',success:function(){
            if(!id) {
                $.showToast('非法操作',false);
                return false;
            }
            $.wpost('/company/delete-company-ajax',{id:id},function(){
                $.showToast('删除成功',true);
                $this.parents('tr').remove();
            });
        }});
    });

    $('.js_set_super_admin').unbind().wclick(function(){
        var $this=$(this);
        var id=$this.attr('data-id');
        var type=$this.attr('data-is-super-admin');
        var title="确定设置超级企业吗?";
        if(type==1){
            title="确定取消超级企业吗?";
        }
        $.confirm({content:title,success:function(){
            if(!id) {
                $.showToast('非法操作',false);
                return false;
            }
            $.wpost('/company/update-super-company-ajax',{id:id,type:type},function(){
                $.showToast('操作成功',true);
                if(type==0){
                    $this.attr('data-is-super-admin',1);
                    $this.html('取消超级企业');
                }else{
                    $this.attr('data-is-super-admin',0);
                    $this.html('设置超级企业');
                }
            });
        }});
    });
});
