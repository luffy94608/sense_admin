$(document).ready(function(){var e=$("#editModal");$(".js_edit").unbind().bind("click",function(){var t=e,n=$(this),r=n.attr("data-edit"),i=t.find(".modal-title"),s="",o="",u="",a=function(e){if(!e.company_privilege||!e.company_privilege.length)return!1;var n=e.company_privilege,r=$("#eui-privilege",t),i="";for(var s=0;s<n.length;s++){var o="",u=n[s].subArr;if(u&&u.length)for(var a=0;a<u.length;a++)o+="<div class='md-checkbox'> <input type='checkbox' id='checkbox_"+u[a].id+"' value='"+u[a].id+"' data-id='"+u[a].id+"' data-name='"+u[a].name+"' class='md-check'> <label for='checkbox_"+u[a].id+"'> <span class='inc'></span> <span class='check'></span> <span class='box'></span> "+u[a].name+" </label></div>";i+="<div class='form-group form-md-checkboxes'> <label>"+n[s].name+"</label> <div class='md-checkbox-inline' > "+o+"</div> </div>"}r.html(i)};if(r==1){var f=JSON.parse(n.attr("data-info"));s=f.id,u=f.name,o=f.privileges,a(f),i.html("修改角色")}else i.html("添加角色");var l=function(e){var n=$("input",t.find("#eui-privilege"));$(n).prop("checked",!1);if(e&&e.length){var r=[];for(var i=0;i<e.length;i++)if(e[i]){var s=e[i].id;r.push(s)}if(n&&n.length)for(var i=0;i<n.length;i++){var o=$(n[i]).val();$.inArray(o,r)!=-1&&$(n[i]).prop("checked",!0)}}};l(o),t.find("#eui-name").val(u);var c=t.find("#eui-confirm");c.unbind().wclick(function(){var e=$("input:checked",t.find("#eui-privilege")),r=[],i=[];for(var o=0;o<e.length;o++){var u=$.trim($(e[o]).val()),a=$.trim($(e[o]).attr("data-name"));r.push(u),i.push({id:u,name:a})}var f={id:s,name:t.find("#eui-name").val(),privilege:r},l=f.name.length;if(l<1||l>30)return $.showToast("角色为1-50个字符",!1),!1;$.wpost("/account/update-role-ajax",f,function(e){$.showToast("添加成功",!0),t.modal("hide");if(s){var r=n.parents("tr").children("td"),o=JSON.parse(n.attr("data-info"));o.name=f.name;var u="";if(i&&i.length){o.privileges=i;for(var a=0;a<i.length;a++)u+=' <span  class="label label-primary">'+i[a].name+"</span>";$(r[1]).html(u)}n.attr("data-info",JSON.stringify(o)),$(r[0]).html(f.name)}else location.reload()})}),e.modal()}),$(".js_delete").unbind().wclick(function(){var e=$(this),t=e.attr("data-id");$.confirm({content:"确认删除吗",success:function(){if(!t)return $.showToast("非法操作",!1),!1;$.wpost("/account/delete-ajax",{id:t,type:2},function(){$.showToast("删除成功",!0),e.parents("tr").remove()})}})})});