/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        listContainer: $('.js_table_list'),
        pagerObj: '',

        uploadImageBtn: '.js_upload_image',
        uploadProgressModal: '#uploadAppProgressModal',

        bannerPreview: $('.js_modal_banner_preview'),
        picPreview: $('.js_modal_pic_preview'),

        inputName : $('#js_modal_name'),
        inputTitle : $('#js_modal_title'),
        inputBanner : $('#js_modal_banner'),
        inputPic : $('#js_modal_pic'),
        inputDemand : $('#js_modal_demand'),
        inputPlan : $('#js_modal_plan'),
        inputAdvantage : $('#js_modal_advantage'),

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));
            var host = document.global_config_data.img_host;
            var banner,pic;
            if(data.banner){
                banner =  host + data.banner;
            }
            if(data.pic){
                pic = host + data.pic;
            }
            init.bannerPreview.attr('src',banner).removeClass('gone');
            init.picPreview.attr('src',pic).removeClass('gone');

            init.inputName.val(data.name);
            init.inputTitle.val(data.title);
            init.inputBanner.val(data.banner);
            init.inputPic.val(data.pic);
            init.inputDemand.val(data.demand);
            init.inputPlan.val(data.plan);
            init.inputAdvantage.val(data.advantage);
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.bannerPreview.addClass('gone').attr('src','');
            init.picPreview.addClass('gone').attr('src','');

            init.inputName.val('');
            init.inputTitle.val('');
            init.inputBanner.val('');
            init.inputPic.val('');
            init.inputDemand.val('');
            init.inputPlan.val('');
            init.inputAdvantage.val('');
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
                        title:$.trim(init.inputTitle.val()),
                        banner:$.trim(init.inputBanner.val()),
                        pic:$.trim(init.inputPic.val()),
                        demand:$.trim(init.inputDemand.val()),
                        plan:$.trim(init.inputPlan.val()),
                        advantage:$.trim(init.inputAdvantage.val()),
                    };

                    var checkMap = {
                        name:'方案名称',
                        title:'简述',
                        banner:'banner',
                        pic:'缩略图',
                        demand:'背景和需求',
                        plan:'解决方案',
                        advantage:'优势',
                    };
                    for (var key in checkMap){
                        if(!params[key]){
                            $.showToast('请输入'+checkMap[key],false);
                            return false;
                        }
                    }

                    $.wpost('/manage/update-solution-ajax',params,function(res){
                        if(!params.id){
                            init.listContainer.prepend(res)
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
                    $.wpost('/manage/delete-solution-ajax',{id:id,type:init.type},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
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
                        $('#'+id).val(data.path);
                        $('.'+id+'_preview').attr('src',data.url).removeClass('gone');
                    }

                    modal.modal('hide');
                    $('.progress-bar',modal).css('width',0);
                },function (percent) {
                    modal.modal({backdrop:'static',keyboard:false});
                    $('.progress-bar',modal).css('width',percent+'%');

                });

            });


        },
        run:function () {
            init.initBtnEvent();
        }
    };

    init.run();
});
