$(document).ready(function(){var e=$("#editModal"),t=function(){$(".eui_del_item",e).unbind().wclick(function(){var e=$(".eui_item_list .eui_item").length;if(e<2)return $.showToast("就剩一个了",!1),!1;$(this).parents(".eui_item").remove()})};$("#eui_add_item",e).unbind().wclick(function(){var n=$(".eui_item_template .eui_item",e).clone();$(".eui_item_list",e).append(n),t()}),$(".js_edit").unbind().bind("click",function(){var n=e,r=$(this),i=r.attr("data-edit"),s=n.find(".modal-title"),o="",u="",a="",f="";if(i==1){var l=JSON.parse(r.attr("data-info"));u=l.action,a=l.name,o=l.id,f=l.subArr,s.html("修改权限")}else s.html("添加权限");var c=function(){n.find("#eui-action").val(""),n.find("#eui-name").val("");var t=$(".eui_item_template .eui_item",e).clone();$(".eui_item_list",e).html(t)},h=function(){n.find("#eui-action").val(u),n.find("#eui-name").val(a);if(f&&f.length){$(".eui_item_list",e).html("");for(var t=0;t<f.length;t++){var r=$(".eui_item_template .eui_item",e).clone();r.find(".eui_item_action").attr("data-id",f[t].id),r.find(".eui_item_action").val(f[t].action),r.find(".eui_item_name").val(f[t].name),$(".eui_item_list",e).append(r)}}};c(),h(),t();var p=n.find("#eui-confirm");e.modal(),p.unbind().wclick(function(){var e={id:o,action:n.find("#eui-action").val(),name:n.find("#eui-name").val()},t=e.action.length;if(t<1||t>30)return $.showToast("action长度为1-30个字符",!1),!1;var i=e.name.length;if(i<1||i>30)return $.showToast("模块名长度为1-30个字符",!1),!1;var s=new Array,u=n.find(".eui_item_list .eui_item");if(u&&u.length)for(var a=0;a<u.length;a++){var f=$(".eui_item_action",$(u[a]))[0],l=$(".eui_item_name",$(u[a]))[0],c=$.trim($(f).attr("data-id")),h=$.trim($(f).val()),p=$.trim($(l).val());if(h&&p){var d=h.length;if(d<1||d>30)return $.showToast("子action长度为1-30个字符",!1),!1;var d=p.length;if(d<1||d>30)return $.showToast("子模块名长度为1-30个字符",!1),!1;s.push({id:c,action:h,name:p})}}if(s.length<1)return $.showToast("请至少添加一个子模块",!1),!1;e.subArr=s,$.wpost("/account/update-privilege-ajax",e,function(t){$.showToast("添加成功",!0),n.modal("hide");if(o){var i=r.parents("tr").children("td");r.attr("data-info",JSON.stringify(t));var u="";if(s&&s.length)for(var a=0;a<s.length;a++)u+="<span class='btn label label-info'>"+s[a].name+"</span>";$(i[0]).html(e.action),$(i[1]).html(e.name),$(i[2]).html(u)}else location.reload()})})}),$(".js_delete").unbind().wclick(function(){var e=$(this),t=e.attr("data-id");$.confirm({content:"确认删除吗",success:function(){if(!t)return $.showToast("非法操作",!1),!1;$.wpost("/account/delete-ajax",{id:t,type:3},function(){$.showToast("删除成功",!0),e.parents("tr").remove()})}})})});