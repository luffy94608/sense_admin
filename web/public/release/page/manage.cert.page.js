$(document).ready(function(){var e={modal:$("#editOrAddModal"),pagerObj:"",listContainer:$(".js_table_list"),uploadImageBtn:".js_upload_image",imgPreview:$(".js_modal_pic_preview"),inputName:$("#js_modal_name"),inputPic:$("#js_modal_pic"),typeParams:"",setModalFormData:function(t){var n=e.modal.find(".modal-title");n.html(n.data("edit")),e.inputName.val(t.name),e.inputPic.val(t.pic);var r="";t.pic&&(r=document.global_config_data.img_host+t.pic),e.imgPreview.attr("src",r).removeClass("gone")},clearModalFormData:function(){var t=e.modal.find(".modal-title");t.html(t.data("new")),e.inputName.val(""),e.inputPic.val(""),e.imgPreview.addClass("gone").attr("src","")},initTabEvent:function(){$(".nav-tabs li").unbind().bind("click",function(){e.typeParams=$(this).data("id"),e.pagerObj.updateList(0)})},initPager:function(){e.pagerObj=$("#wrapperPageList").Pager({protocol:"/manage/get-cert-list-ajax",listSize:20,onPageInitialized:function(){if(e.pagerObj){var t=$(window).scrollTop();t>100&&$(window).scrollTop(0)}},wrapUpdateData:function(t,n){var r=$(".nav-tabs li.active").data("id");e.typeParams.toString().length&&(r=e.typeParams);var i={type:r};return i&&$.extend(n,i),n}}),e.pagerObj.updateList(0)},initBtnEvent:function(){$(document).on("click",".js_edit",function(){e.clearModalFormData();var t=e.modal.find(".js_submit"),n=$(this),r=n.parents("tr"),i=r.data("id"),s=r.data("info");i&&s&&e.setModalFormData(s),e.modal.modal(),t.unbind().bind("click",function(){var t={id:i,name:$.trim(e.inputName.val()),pic:$.trim(e.inputPic.val()),type:$(".nav-tabs li.active").data("id")},n={name:"请输入描述",pic:"请上传图片"};for(var s in n)if(!t[s]||t[s].length<1)return $.showToast(n[s],!1),!1;$.wpost("/manage/update-cert-ajax",t,function(n){t.id?r.replaceWith(n):e.listContainer.append(n),$.showToast("保存成功",!0),e.modal.modal("hide")})})}),$(document).on("click",".js_delete",function(){var e=$(this),t=e.parents("tr"),n=t.attr("data-id");$.confirm({content:"确认删除吗",success:function(){$.wpost("/manage/delete-cert-ajax",{id:n},function(){$.showToast("删除成功",!0),e.parents("tr").remove()})}})}),$(document).on("click",".js_up,.js_down",function(){var e=$(this),t=$(e.parents(".js_modal_param")[0]);t.length==0&&(t=$(e.parents("tr")));var n;e.hasClass("js_up")?n=t.prev()[0]:n=t.next()[0];if(!n)return!1;e.hasClass("js_up")?$(n).before(t):$(n).after(t)}),$(document).on("click",".js_sort_save",function(){var e=$(".js_table_list tr"),t=[];for(var n=0;n<e.length;n++){var r=$(e[n]),i=r.data("id");if(i){var s={id:i,sort_num:n+1};t.push(s)}}if(t.length<1)return $.showToast("无可排序的数据",!1),!1;$.wpost("/manage/save-cert-sort-ajax",{params:t},function(){$.showToast("保存成功",!0)})}),$(e.uploadImageBtn).unbind().bind("click",function(){var t=$(this),n=$(e.uploadProgressModal);$(this).uploadImage("/upload/upload-image",{request_type:"ajax"},function(r){var i=t.data("id");r.res&&(e.inputPic.val(r.path),e.imgPreview.attr("src",r.url).removeClass("gone")),n.modal("hide"),$(".progress-bar",n).css("width",0)},function(e){n.modal({backdrop:"static",keyboard:!1}),$(".progress-bar",n).css("width",e+"%")})})},run:function(){e.initPager(),e.initTabEvent(),e.initBtnEvent()}};e.run()});