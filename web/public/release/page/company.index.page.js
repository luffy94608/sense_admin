$(document).ready(function(e){var t=e("#editModal");e(".js_edit").unbind().bind("click",function(){var n=t,r=e(this),i=r.attr("data-edit"),s=n.find(".modal-title"),o="",u="",a="",f="";if(i==1){var l=JSON.parse(r.attr("data-info"));o=l.id,a=l.name,f=l.domain,u=l.privileges,s.html("修改企业信息")}else s.html("添加企业信息");var c=function(t){var r=e("input",n.find("#eui-privilege"));e(r).prop("checked",!1);if(t&&t.length){var i=[];for(var s=0;s<t.length;s++)if(t[s]){var o=t[s].id;i.push(o)}if(r&&r.length)for(var s=0;s<r.length;s++){var u=e(r[s]).val();e.inArray(u,i)!=-1&&e(r[s]).prop("checked",!0)}}};c(u),n.find("#eui-name").val(a),n.find("#eui-domain").val(f);var h=n.find("#eui-confirm");h.unbind().wclick(function(){var t=e("input:checked",n.find("#eui-privilege")),i=[],s=[];for(var u=0;u<t.length;u++){var a=e.trim(e(t[u]).val()),f=e.trim(e(t[u]).attr("data-name"));i.push(a),s.push({id:a,name:f})}var l={id:o,name:e.trim(n.find("#eui-name").val()),domain:e.trim(n.find("#eui-domain").val()),privilege:i},c=l.name.length;if(c<1||c>30)return e.showToast("角色为1-50个字符",!1),!1;e.wpost("/company/update-company-ajax",l,function(t){e.showToast("添加成功",!0),n.modal("hide");if(o){var i=r.parents("tr").children("td"),u=JSON.parse(r.attr("data-info"));u.name=l.name,u.domain=l.domain;var a="";if(s&&s.length){u.privileges=s;for(var f=0;f<s.length;f++)a+=' <span  class="label label-primary">'+s[f].name+"</span>";e(i[2]).html(a)}r.attr("data-info",JSON.stringify(u)),e(i[0]).html(l.name),e(i[1]).html(l.domain)}else location.reload()})}),t.modal()}),e(".js_delete").unbind().wclick(function(){var t=e(this),n=t.attr("data-id");e.confirm({content:"确认删除吗",success:function(){if(!n)return e.showToast("非法操作",!1),!1;e.wpost("/company/delete-company-ajax",{id:n},function(){e.showToast("删除成功",!0),t.parents("tr").remove()})}})}),e(".js_set_super_admin").unbind().wclick(function(){var t=e(this),n=t.attr("data-id"),r=t.attr("data-is-super-admin"),i="确定设置超级企业吗?";r==1&&(i="确定取消超级企业吗?"),e.confirm({content:i,success:function(){if(!n)return e.showToast("非法操作",!1),!1;e.wpost("/company/update-super-company-ajax",{id:n,type:r},function(){e.showToast("操作成功",!0),r==0?(t.attr("data-is-super-admin",1),t.html("取消超级企业")):(t.attr("data-is-super-admin",0),t.html("设置超级企业"))})}})})});