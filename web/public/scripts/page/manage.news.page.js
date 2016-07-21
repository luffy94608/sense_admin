/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        listContainer: $('.js_table_list'),
        pagerObj: '',
        ckEditor:'',
        inputName : $('#js_modal_name'),
        inputTime : $('#js_modal_time'),
        inputContent : $('#js_modal_content'),

        /**
         * 初始化时间插件
         */
        initDatePickerEvent:function () {
            init.inputTime.datepicker({
                // todayBtn: 'linked',
                todayHighlight: true,
                format: 'yyyy-mm-dd',
                clearBtn: true,
                autoclose: true
            });
        },

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));

            init.inputName.val(data.title);
            init.inputTime.val(data.time);

            init.ckEditor.setData(data.content);
            // init.inputContent.val(data.content);
            // setTimeout(function () {
            //     init.UE.setContent(data.content);
            // },500);
            //
            // init.UE.reset();
            // setTimeout(function(){
            //     init.UE.setContent(data.content);
            // },200);


        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.inputName.val('');
            init.inputTime.val('');
            // init.inputContent.val('');

            // if (CKEDITOR.instances['js_modal_content']) {
            //     //CKEDITOR.remove(CKEDITOR.instances['editor']); //Does the same as line below
            //     delete CKEDITOR.instances['js_modal_content'];
            // }
            if(init.ckEditor){
                init.ckEditor.destroy('js_modal_content');
            }
            init.ckEditor = CKEDITOR.replace('js_modal_content',{
                language: 'zh-cn'
            });


        },

        /**
         * 初始化分页
         */
        initPager : function () {
            init.pagerObj = $('#wrapperPageList').Pager({
                protocol:'/manage/get-news-list-ajax',
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
                init.modal.modal({backdrop:'static',keyboard:false});

                if(id && info){
                    init.setModalFormData(info);
                }

                submitBtn.unbind().bind('click',function () {
                    var params = {
                        id:id,
                        title:$.trim(init.inputName.val()),
                        time:$.trim(init.inputTime.val()),
                        // content:$.trim(init.inputContent.val()),
                        content:init.ckEditor.getData()
                    };
                    var checkMap = {
                        title:'标题',
                        time:'新闻时间',
                        content:'新闻内容',
                    };
                    for (var key in checkMap){
                        if(!params[key]){
                            $.showToast('请输入'+checkMap[key],false);
                            return false;
                        }
                    }

                    $.wpost('/manage/update-news-ajax',params,function(res){
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

            init.modal.on('hidden.bs.modal', function () {
                // 关闭Dialog前移除编辑器
                console.log('close')
                $(this).removeData('bs.modal');
                // init.UE.setContent('');
                // init.UE.destroy();
            });

            /**
             * 删除
             */
            $(document).on('click','.js_delete',function () {
                var $this=$(this);
                var $parent=$this.parents('tr');
                var id=$parent.attr('data-id');
                $.confirm({content:'确认删除吗',success:function(){
                    $.wpost('/manage/delete-news-ajax',{id:id,type:init.type},function(){
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
                if($this.hasClass('js_up')){
                    $(target).before($parent);
                }else{
                    $(target).after($parent);
                }
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
                $.wpost('/manage/save-news-sort-ajax',{params:params},function(){
                    $.showToast('保存成功',true);
                });
            });

        },
        run:function () {



            init.initDatePickerEvent();
            init.initPager();

            init.initBtnEvent();
        }
    };

    init.run();

});
