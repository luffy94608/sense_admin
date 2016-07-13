/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        pagerObj: '',
        listContainer: $('.js_table_list'),
        uploadImageBtn: '.js_upload_image',

        imgPreview : $('.js_modal_pic_preview'),
        inputName : $('#js_modal_name'),
        inputPic : $('#js_modal_pic'),

        typeParams : '',


        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));
            init.inputName.val(data.name);
            init.inputPic.val(data.pic);
            var img = '';
            if(data.pic){
                img = document.global_config_data.img_host + data.pic;
            }
            init.imgPreview.attr('src',img).removeClass('gone');

        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.inputName.val('');
            init.inputPic.val('');
            init.imgPreview.addClass('gone').attr('src','');
        },

        initTabEvent : function () {
          $('.nav-tabs li').unbind().bind('click',function () {
              init.typeParams = $(this).data('id');
              setTimeout(function () {
                  init.pagerObj.updateList(0);
              },500)
          });
        },

        /**
         * 详情pager
         */
        initPager : function () {
            init.pagerObj = $('#wrapperPageList').Pager({
                protocol:'/manage/get-cert-list-ajax',
                listSize:20,
                onPageInitialized:function(){
                    if (init.pagerObj){
                        var top=$(window).scrollTop();
                        if(top>100){
                            $(window).scrollTop(0);
                        }
                    }
                },
                wrapUpdateData:function(idx,data){
                    var params = {
                        type:$('.nav-tabs li.active').data('id'),
                    };

                    if (params){
                        $.extend(data, params);
                    }
                    return data;
                }
            });

            init.pagerObj.updateList(0);
        },

        /**
         * 初始化按钮时间
         */
        initBtnEvent : function () {

            /**
             * 新建或者编辑
             */
            $(document).on('click','.js_edit',function () {
                init.clearModalFormData();
                var submitBtn = init.modal.find('.js_submit');
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.data('id');
                var info=$parent.data('info');
                if(id && info){
                    init.setModalFormData(info);
                }
                init.modal.modal();
                submitBtn.unbind().bind('click',function () {
                    var params = {
                        id:id,
                        name:$.trim(init.inputName.val()),
                        pic:$.trim(init.inputPic.val()),
                        type:$('.nav-tabs li.active').data('id'),
                    };
                    var checkMap = {
                        name:'请输入描述',
                        pic:'请上传图片',
                    };

                    for (var key in checkMap){
                        if(!params[key] || params[key].length<1){
                            $.showToast(checkMap[key],false);
                            return false;
                        }
                    }
                    $.wpost('/manage/update-cert-ajax',params,function(res){
                        if(!params.id){
                            init.listContainer.append(res)
                        }else{
                            $parent.replaceWith(res)
                        }
                        $.showToast('保存成功',true);
                        init.modal.modal('hide');
                    });
                });
            });

            /**
             * 删除
             */
            $(document).on('click','.js_delete',function () {
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.attr('data-id');
                $.confirm({content:'确认删除吗',success:function(){
                    $.wpost('/manage/delete-cert-ajax',{id:id},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });
            /**
             * 上升 下降
             */
            $(document).on('click','.js_up,.js_down',function () {
                var $this=$(this);
                var $parent=$($this.parents('.js_modal_param')[0]);
                if($parent.length == 0){
                    $parent=$($this.parents('tr'));
                }
                var target;

                if($this.hasClass('js_up')){
                    target = $parent.prev()[0];
                }else{
                    target = $parent.next()[0];
                }
                if(!target){
                    return false;
                }
                target = $(target);
                var bakTarget = target.clone();
                var bakCurrent =  $parent.clone();

                target.replaceWith(bakCurrent);
                $parent.replaceWith(bakTarget);
            });

            /**
             * 保存排序
             */
            $(document).on('click','.js_sort_save',function () {
                var trNodes = $('.js_table_list tr');

                var params = [];
                for (var i=0;i<trNodes.length;i++){
                    var tmpNode = $(trNodes[i]);
                    var tmpId = tmpNode.data('id');
                    if(tmpId)
                    {
                        var tmpData = {
                            id:tmpId,
                            sort_num:i+1,
                        };
                        params.push(tmpData);
                    }

                }
                if(params.length<1){
                    $.showToast('无可排序的数据',false);
                    return false;
                }
                $.wpost('/manage/save-cert-sort-ajax',{params:params},function(){
                    $.showToast('保存成功',true);
                });
            });

            /**
             * 上传文件
             */

            $(init.uploadImageBtn).unbind().bind('click',function(){
                var $this = $(this);
                var modal = $(init.uploadProgressModal);
                $(this).uploadImage('/upload/upload-image',{request_type:'ajax'},function (data) {
                    var id = $this.data('id');
                    if(data.res){
                        init.inputPic.val(data.path);
                        init.imgPreview.attr('src',data.url).removeClass('gone');
                    }

                    modal.modal('hide');
                    $('.progress-bar',modal).css('width',0);
                },function (percent) {
                    modal.modal({backdrop:'static',keyboard:false});
                    $('.progress-bar',modal).css('width',percent+'%');

                });

            });

        },
        run : function () {
            init.initPager();
            init.initTabEvent();
            init.initBtnEvent();
        }
    };

    init.run();
});
