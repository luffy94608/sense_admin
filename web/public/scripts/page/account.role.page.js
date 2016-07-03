/**
 * Created by luffy on 15/7/16.
 */
$(document).ready(function(){
    var $modal=$('#editModal');
    $('.js_edit').unbind().bind('click',function(){
        var modal = $modal;
        var button = $(this); // Button that triggered the modal
        var edit=button.attr('data-edit');
        var title=modal.find('.modal-title');
        var id='',privileges='',name='';

        var initPrivilegeHtml=function(data)
        {
            if(!(data.company_privilege && data.company_privilege.length))
            {
                return false;
            }
            var privilegeList=data.company_privilege;
            var privilegeBody=$('#eui-privilege',modal);
            var privilegeHtml="";
            for(var i=0;i<privilegeList.length;i++){
                var subArrHtml="";
                var subArrList=privilegeList[i].subArr;
                if(subArrList && subArrList.length)
                {
                    for(var j=0;j<subArrList.length;j++)
                    {
                         subArrHtml+="<div class='md-checkbox'> <input type='checkbox' id='checkbox_"+subArrList[j].id+"' value='"+subArrList[j].id+"' data-id='"+subArrList[j].id+"' data-name='"+subArrList[j].name+"' class='md-check'> <label for='checkbox_"+subArrList[j].id+"'> <span class='inc'></span> <span class='check'></span> <span class='box'></span> "+subArrList[j].name+" </label></div>";
                    }
                }
                privilegeHtml+="<div class='form-group form-md-checkboxes'> <label>"+privilegeList[i].name+"</label> <div class='md-checkbox-inline' > "+subArrHtml+"</div> </div>";
            }
            privilegeBody.html(privilegeHtml);
        };
        if(edit==1){
            var info=JSON.parse(button.attr('data-info'));
            id=info.id;
            name=info.name;
            privileges=info.privileges;
            initPrivilegeHtml(info);
            title.html('修改角色');
        }else{
            title.html('添加角色');
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
                name:modal.find('#eui-name').val(),
                privilege:pArr
            };
            var aLength=data.name.length;
            if(aLength<1 || aLength>30){
                $.showToast('角色为1-50个字符',false);
                return false;
            }
            $.wpost('/account/update-role-ajax',data,function(res){
                $.showToast('添加成功',true);
                modal.modal('hide');
                if(id){
                    var tds=button.parents('tr').children('td');
                    var srcPdata=JSON.parse(button.attr('data-info'));
                    srcPdata.name=data.name;
                    var pStr='';
                    if(pResult && pResult.length){
                        srcPdata.privileges=pResult;
                        for(var i=0;i<pResult.length;i++){
                            pStr+=' <span  class="label label-primary">'+pResult[i].name+'</span>';
                        }
                        $(tds[1]).html(pStr);
                    }

                    button.attr('data-info',JSON.stringify(srcPdata));
                    $(tds[0]).html(data.name);

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
            $.wpost('/account/delete-ajax',{id:id,type:2},function(){
                $.showToast('删除成功',true);
                $this.parents('tr').remove();
            });
        }});
    });
});