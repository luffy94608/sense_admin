/**
 * xiufei.lu
 */
$(document).ready(function(){
    var init = {
        modal: $('#editOrAddModal'),
        listContainer: $('.js_table_list'),
        pagerObj: '',

        inputName : $('#js_modal_name'),
        inputLocation : $('#js_modal_location'),
        inputCount : $('#js_modal_count'),
        inputExperience : $('#js_modal_experience'),
        inputDegree : $('#js_modal_degree'),
        inputProperty : $('#js_modal_property'),
        inputSalary : $('#js_modal_salary'),
        inputDescription : $('#js_modal_description'),
        inputRequirement : $('#js_modal_requirement'),

        /**
         *初始化表单
         */
        setModalFormData : function (data) {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('edit'));

            init.inputName.val(data.title);
            init.inputLocation.val(data.location);
            init.inputCount.val(data.num);
            init.inputExperience.val(data.experience);
            init.inputDegree.val(data.degree);
            init.inputProperty.val(data.nature);
            init.inputSalary.val(data.salary);
            init.inputDescription.val(data.duty);
            init.inputRequirement.val(data.requirement);
        },
        /**
         *清空表单
         */
        clearModalFormData : function () {
            var modalTitle = init.modal.find('.modal-title');
            modalTitle.html(modalTitle.data('new'));
            init.inputName.val('');
            init.inputLocation.val('');
            init.inputCount.val('');
            init.inputExperience.val('');
            init.inputDegree.val('');
            init.inputProperty.val('');
            init.inputSalary.val('');
            init.inputDescription.val('');
            init.inputRequirement.val('');
        },

        /**
         * 初始化分页
         */
        initPager : function () {
            init.pagerObj = $('#wrapperPageList').Pager({
                protocol:'/manage/get-recruit-list-ajax',
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
                if(id && info){
                    init.setModalFormData(info);
                }

                init.modal.modal();
                submitBtn.unbind().bind('click',function () {
                    var params = {
                        id:id,
                        title:$.trim(init.inputName.val()),
                        location:$.trim(init.inputLocation.val()),
                        num:$.trim(init.inputCount.val()),
                        experience:$.trim(init.inputExperience.val()),
                        degree:$.trim(init.inputDegree.val()),
                        nature:$.trim(init.inputProperty.val()),
                        salary:$.trim(init.inputSalary.val()),
                        duty:$.trim(init.inputDescription.val()),
                        requirement:$.trim(init.inputRequirement.val()),
                    };
                    var checkMap = {
                        title:'职位名称',
                        location:'工作地点',
                        num:'招聘人数',
                        experience:'工作经验',
                        degree:'学历要求',
                        nature:'工作性质',
                        salary:'薪资范围',
                        duty:'岗位职责',
                        requirement:'岗位要求',
                    };
                    for (var key in checkMap){
                        if(!params[key]){
                            $.showToast('请输入'+checkMap[key],false);
                            return false;
                        }
                    }

                    $.wpost('/manage/update-recruit-ajax',params,function(res){
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
                    $.wpost('/manage/delete-recruit-ajax',{id:id,type:init.type},function(){
                        $.showToast('删除成功',true);
                        $this.parents('tr').remove();
                    });
                }});
            });

        },
        run:function () {
            init.initPager();
            init.initBtnEvent();
        }
    };

    init.run();
});
