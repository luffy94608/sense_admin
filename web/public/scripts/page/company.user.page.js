/**
 * xiufei.lu
 */

$(document).ready(function($){
    var table='';
    var initDataTables=function(){
        if(table){
            table.fnDestroy();
        }
        table=$('#account_table_1').dataTable({
            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": 以升序排列此列",
                    "sortDescending": ": 以降序排列此列"
                },
                "emptyTable": "表中数据为空",
                "info": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "infoFiltered": "(由 _MAX_ 条数据中检索)",
                "lengthMenu": "显示 _MENU_ 数据",
                "search": "搜索:",
                "zeroRecords": "没有匹配结果",
                "paging": {
                    "first": "首页",
                    "previous": "上一页",
                    "next": "下一页",
                    "last": "末页"
                }
            },

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

            "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 20,
            "columnDefs": [{  // set default column settings
                'orderable': false,
                'targets': [0,1,2,3,5]
            }, {
                "searchable": false,
                "targets": [4,5]
            }],
            "order": [
                [4, "desc"]
            ] // set first column as a default sort by asc
        });
    };
    initDataTables();

    var $modal=$('#editUserInfoModal');

    $(document).on('click','.js_edit',function(){
        var modal = $modal;
        var button = $(this); // Button that triggered the modal
        var submitBtn=modal.find('#eui-confirm');
        var title=modal.find('.modal-title');
        var edit=button.attr('data-edit');
        var id;

        if(edit==1){
            title.html('修改管理员');
        }else{
            title.html('添加管理员');
        }
        var initModalData=function(){
            var jsonData=button.attr('data-json');
            var initInfo;
            if(jsonData){
                initInfo=JSON.parse(button.attr('data-json'));
                id=(initInfo && initInfo.id)?initInfo.id:'';
            }
            var initData={
                account:(initInfo && initInfo.account)?initInfo.account:'',
                password:(initInfo && initInfo.password)?initInfo.password:'',
                name:(initInfo && initInfo.name)?initInfo.name:'',
                phone:(initInfo && initInfo.phone)?initInfo.phone:'',
                cid:(initInfo && initInfo.cid)?initInfo.cid:''
            };
            modal.find('#eui-account').val(initData.account);
            modal.find('#eui-password').val('');
            modal.find('#eui-name').val(initData.name);
            modal.find('#eui-mobile').val(initData.phone);
            modal.find('#eui-company').val(initData.cid);
        };
        initModalData();
        submitBtn.unbind().wclick(function(){
            var data={
                id:id,
                account: $.trim(modal.find('#eui-account').val()),
                password: $.trim(modal.find('#eui-password').val()),
                name:modal.find('#eui-name').val(),
                phone:modal.find('#eui-mobile').val(),
                cid:modal.find('#eui-company').val(),
                company_name:$('#eui-company option:checked').html()
            };
            var status = $.checkInputVal({val:data.account,type:'email',onChecked:function (value,state,hint) {
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }});
            if(status<=0){
                return false;
            }

            var pLength=data.password.length;
            if(!data.id){
                if(pLength<1 || pLength>50){
                    $.showToast('密码长度为1-50个字符',false);
                    return false;
                }
            }

            var status = $.checkInputVal({val:data.name,type:'name',onChecked:function (value,state,hint) {
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }});
            if(status<=0){
                return false;
            }

            var status = $.checkInputVal({val:data.phone,type:'mobile',onChecked:function (value,state,hint) {
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }});
            if(status<=0){
                return false;
            }


            if(!(data.cid && data.cid.length)){
                $.showToast('请选择企业',false);
                return false;
            }
            $.wpost('/company/update-admin-ajax',data,function(res){
                modal.modal('hide');
                if(id){
                    $.showToast('修改成功',true);
                    button.attr('data-json',JSON.stringify(data));
                    var tds=button.parents('tr').children('td');
                    $(tds[0]).html(data.account);
                    $(tds[1]).html(data.name);
                    $(tds[2]).html(data.phone);
                    $(tds[3]).html(data.company_name);
                }else{
                    $.showToast('添加成功',true);
                    location.reload();
                }
            });
        });
        modal.modal();
    });


    /**
     * 删除操作
     */
    $(document).on('click','.js_delete',function(){
        var $this=$(this);
        var id=$this.attr('data-id');

        $.confirm({content:'确认删除吗',success:function(){
            if(!id)
            {
                $.showToast('非法操作',false);
                return false;
            }
            $.wpost('/company/delete-admin-ajax',{id:id,type:1},function(){
                $.showToast('删除成功',true);
                table.fnDeleteRow($this.parents('tr'));//删除datatables数据
                //$this.parents('tr').remove();
            });
        }});
    });



});
