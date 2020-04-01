@extends("layouts.dev")
@push("css")
    <link rel="stylesheet" type="text/css" href="{{asset('css/dev/includes/acl.css')}}">
@endpush
@section('content')
@csrf

<div class="row justify-content-center">
        <div class="card">
            <div class="card-header font-weight-bold">ACL</div>

            <div class="card-body form-group">
                <fieldset>
                    <div class="col-md-12 filter row">
                        <div class="col-md-12 form-group row">
                            <div class="col-md-1 form-title">Role</div>
                            <div class="col-md-4">
                                <select id="cb-role" class="form-control">
                                    <option value="">---</option>
                                    <?php if(!empty($roleList)){?>
                                    <?php foreach ($roleList as $roleItem){?>
                                    <option value="<?php echo $roleItem->name;?>"
                                            data-role_id="<?php echo $roleItem->role_value;?>"
                                    @if($role===$roleItem->name) {{"selected"}} @endif>
                                        <?php echo $roleItem->name?>
                                    </option>
                                    <?php   }
                                    }?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button id="add-role" class="btn btn-primary pull-left fa fa-plus"
                                        title="Add new role"></button>
                            </div>
                            <div class="col-md-2 form-title">Module</div>
                            <div class="col-md-4">
                                <select id="cb-module" class="lang form-control">
                                    <option value="">---</option>
                                    <?php if(!empty($moduleList)){?>
                                    <?php foreach ($moduleList as $moduleItem){?>
                                    <option value="<?php echo $moduleItem->module_code;?>"
                                    @if($module===$moduleItem->module_code) {{"selected"}} @endif >
                                        <?php echo $moduleItem->module_code?>
                                    </option>
                                    <?php   }
                                    }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 form-group row">
                            <div class="col-md-1 form-title">Controller</div>
                            <div class="col-md-4">
                                <input type="text" id="text-controller" class="form-control" value="{{$controller}}"/>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-2 form-title">Action</div>
                            <div class="col-md-4">
                                <input type="text" id="text-action" class="form-control" value="{{$action}}"/>
                            </div>
                        </div>
                    </div>


                </fieldset>
                <div class="col-md-12 row action">
                    <div class="pull-left padding-left-0 col-md-6 left-action">
                        <select class="form-control" id="limit">
                            <option
                                value="{{\App\Dev\Common\DevRoleConst::limit_20}}" @if($limit==\App\Dev\Common\DevRoleConst::limit_20) {{"selected"}} @endif >
                                {{\App\Dev\Common\DevRoleConst::limit_20}}
                            </option>
                            <option
                                value="{{\App\Dev\Common\DevRoleConst::limit_50}}" @if($limit==\App\Dev\Common\DevRoleConst::limit_50) {{"selected"}} @endif>
                                {{\App\Dev\Common\DevRoleConst::limit_50}}
                            </option>
                            <option
                                value="{{\App\Dev\Common\DevRoleConst::limit_70}}" @if($limit==\App\Dev\Common\DevRoleConst::limit_70) {{"selected"}} @endif>
                                {{\App\Dev\Common\DevRoleConst::limit_70}}
                            </option>
                            <option
                                value="{{\App\Dev\Common\DevRoleConst::limit_100}}" @if($limit==\App\Dev\Common\DevRoleConst::limit_100) {{"selected"}} @endif>
                                {{\App\Dev\Common\DevRoleConst::limit_100}}
                            </option>
                        </select>
                        <button id="refresh" class="btn btn-primary">Synchronously <span
                                class="fa fa-refresh"></span></button>
                        <button id="generation" class="btn btn-primary">Generate to config file <span
                                class="fa fa-file"></span></button>
                        <button id="clear_filter" class="btn btn-danger">Clear filter <span
                                class="fa fa-times-circle"></span></button>
                    </div>
                    <div class="text-right col-md-6 right-action">
                        <label style="color: red">Active for all of screen ( both screen filtered and not filtered
                            ): </label>
                        <label class="switch">
                            <input type="checkbox" class="change-active-all">
                            <span class="slider round">
                        </span>
                        </label>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="acl-table" class="table table-striped jambo_table table-hover table-user table-bordered">
                        <thead>
                        <tr class="headings">
                            <th class="column-title">No</th>
                            <th class="column-title">Role name</th>
                            <th class="column-title">Module</th>
                            <th class="column-title">Controller</th>
                            <th class="column-title">Action</th>
                            <th class="column-title">Namespace</th>
                            <th class="column-title">
                                <label class="switch">
                                    <input type="checkbox"
                                           class="active_by_key" @if($checked==\App\Dev\Common\DevRoleConst::true) {{"checked"}} @endif>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $index = 0; ?>
                        <?php if(!empty($dataAcl[1])){
                        foreach ($dataAcl[1] as $item){
                        $index++;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $index; ?></td>
                            <td><?php echo $item->role_name; ?></td>
                            <td><?php echo $item->module; ?></td>
                            <td><?php echo $item->controller; ?></td>
                            <td><?php echo $item->action; ?></td>
                            <td><?php echo $item->screen_code; ?></td>
                            <td class="text-center">
                                <?php
                                $selected = '';
                                if ($item->is_active == 1) {
                                    $selected = "checked";
                                }
                                ?>
                                <label class="switch">
                                    <input type="checkbox" class="change-active" <?php echo $selected; ?>
                                    data-role_map_id="<?php echo $item->role_map_id;?>">
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        </tr>
                        <?php }
                        }?>
                        </tbody>
                    </table>
                </div>
                <div class="paginate float-right">
                    {{ $dataAcl[1]->appends(Request::except('page'))->links() }}
                </div>
            </div>
        </div>
</div>


@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#acl-table').DataTable(
        {
            scrollY:        '60vh',
            scrollCollapse: true,
            fixedHeader:    true,
            bJQueryUI:      true,
            info:           false,
            paging:         false,
            dom:            't',
            searching:      true,
            "columnDefs":   [{
                "targets":   [5, 6],
                "orderable": false
            }]
        }
        );

        $('#cb-role').on('change', function () {
            filter();
        });

        $('#cb-module').on('change', function () {
            filter();
        });

        $('#text-controller').on('change', function () {
            filter();
        });
        $('#text-action').on('change', function () {
            filter();
        });

        $(document).on('change','#limit',function () {
            filter();
        });

        $(document).on('change', '.change-active', function () {
            var data = {
                active: $(this).prop('checked'),
                role_map_id: $(this).data('role_map_id')
            };
            $.ajax({
                data: data,
                type: 'Post',
                dataType: 'json',
                url: "<?php echo @route('updateAclActive')?>",
                success: function (result) {
                }
            });
        });

        //=======================active by key=============================
        $(document).on('change', '.active_by_key', function () {
            var checked     = $(this).prop('checked');
            var role        = $("#cb-role option:selected").data("role_id");
            var arrScreenId = [];
            $.confirm({
                          title         : "Confirm",
                          icon          : 'fa fa-exclamation-circle',
                          boxWidth      : '30%',
                          useBootstrap  : false,
                          type          : "orange",
                          closeIcon     : true,
                          closeIconClass: 'fa fa-close close_confirm_key',
                          content       : "Are you sure? All of this filters will be changed?",
                          buttons       : {
                              Save  : {
                                  text    : "OK",
                                  btnClass: 'btn btn-primary',
                                  action  : function () {
                                      $('#acl-table tbody .change-active').each(function(){
                                          arrScreenId.push($(this).data("role_map_id"));
                                      });
                                      var data = {
                                          active: checked,
                                          arrScreenId:arrScreenId,
                                          role:role
                                      };
                                      $.ajax({
                                                 data: data,
                                                 type: 'Post',
                                                 dataType: 'json',
                                                 url: "<?php echo @route('updateAclFilter')?>",
                                                 success: function (data) {
                                                     notify('Success', 'success', data.message, '#27A836', '#27A836');
                                                     if(checked==true){
                                                         $('#acl-table tbody .change-active').each(function(){
                                                             $(this).prop('checked',true);
                                                         });
                                                     }else{
                                                         $('#acl-table tbody  .change-active').each(function(){
                                                             $(this).prop('checked',false);
                                                         });
                                                     }
                                                 }
                                             });
                                  }
                              },
                              cancel: {
                                  text    : "Cancel",
                                  btnClass: 'btn btn-default',
                                  action  : function () {
                                      var checked     = $(".active_by_key").prop('checked');
                                      (checked==true)?$(".active_by_key").prop("checked",false):$(".active_by_key").prop("checked",true);
                                  }
                              }
                          }
                      });
        });

        //=======================event when close confirm =================
        $(document).on("click",".close_confirm_all",function () {
            var checked     = $(".change-active-all").prop('checked');
            (checked==true)?$(".change-active-all").prop("checked",false):$(".change-active-all").prop("checked",true);
        })
        $(document).on("click",".close_confirm_key",function () {
            var checked     = $(".active_by_key").prop('checked');
            (checked==true)?$(".active_by_key").prop("checked",false):$(".active_by_key").prop("checked",true);
        })
        //=======================active all ===============================
        $(document).on('change', '.change-active-all', function () {
            var checked =  $(this).prop('checked');
            var data = {
                active: checked
            };
            console.log(checked);
            var confirm = $.confirm({
                          title         : '<p class="text-warning">Warning</p>',
                          icon          : 'fa fa-exclamation-circle',
                          boxWidth      : '40%',
                          useBootstrap  : false,
                          type          : "orange",
                          closeIcon     : true,
                          closeIconClass: 'fa fa-close close_confirm_all',
                          content       : '' + '<style>.form-content{ height : 110px; }</style>' +
                                          '<div class="form-content" id="form-content">' +
                                          '<label>\Active for all of screen ( both screen filtered and not filtered )?\</label>' + '<br>' +
                                          '<label>Enter "yes" to confirm</label>' +
                                          '<input type="text" placeholder="" class="text_submit form-control" required />' +
                                          '</div>',
                          buttons       : {
                              Save  : {
                                  text    : "Yes",
                                  btnClass: 'btn btn-primary',
                                  action  : function () {
                                      var text_submit = this.$content.find('.text_submit').val().toLowerCase();
                                      if (text_submit === "yes") {
                                          $.ajax({
                                                     data    : data,
                                                     type    : 'Post',
                                                     dataType: 'json',
                                                     url     : "<?php echo @route('updateAclActiveAll')?>",
                                                     success : function (result) {
                                                         if(result.status == '{{\App\Core\Common\SDBStatusCode::OK}}'){
                                                             notify("Success", 'success', result.message, '#437F2C', '#3E943C');
                                                             $('#cb-role').val('');
                                                             $('#cb-module').val('');
                                                             $('#text-controller').val('');
                                                             $('#text-action').val('');
                                                             if (checked == true) {
                                                                 $(".active_by_key").prop("checked",true);
                                                                 $('#acl-table tbody .change-active').each(function () {
                                                                     $(this).prop('checked', true);
                                                                 });
                                                             } else {
                                                                 $(".active_by_key").prop("checked",false);
                                                                 $('#acl-table tbody  .change-active').each(function () {
                                                                     $(this).prop('checked', false);
                                                                 });
                                                             }
                                                         }else{
                                                             notify("Error", 'error',result.message, '#AA3131', '#792A2A');
                                                         }
                                                     },
                                                     error: function error(xhr, ajaxOptions, thrownError) {
                                                         console.log('Error ' + xhr.status + ' | ' + thrownError);
                                                     }
                                                 });
                                      } else {
                                          (checked == true) ? $(".change-active-all").prop("checked", false) : $(".change-active-all").prop("checked", true);
                                          confirm.close();
                                      }
                                  }
                              },
                              cancel: {
                                  text    : "Cancel",
                                  btnClass: 'btn btn-default',
                                  action  : function () {
                                      var checked     = $(".change-active-all").prop('checked');
                                      (checked==true)?$(".change-active-all").prop("checked",false):$(".change-active-all").prop("checked",true);
                                  }
                              }
                          }
                      });
        });

        $(document).on('click', '#generation', function () {
            $.ajax({
                type: 'Post',
                url: "<?php echo @route('generationAclFile')?>",
                success: function (result) {
                    $.alert(
                    {
                        title: 'Alert!',
                        content: 'Gennerated!',
                    }
                    );
                }
            });
        });

        $(document).on('click', '#refresh', function () {
            $.ajax({
                type: 'Post',
                url: "<?php echo @route('refreshAclInDB')?>",
                success: function (result) {
                    $.alert(
                    {
                        title: 'Alert!',
                        content: 'Synchronized!',
                        buttons: {
                            ok: function(){
                                location.reload();
                            }
                        }
                    }
                    );
                }
            });
        });

        //==============================event clear filter =================================
        $(document).on('click','#clear_filter',function () {
            var limit       = $("#limit").val();
            var stringLimit = (limit != "" && limit!={{\App\Dev\Common\DevRoleConst::limit_20}}) ? "limit=" + limit : "";
            window.location.search = stringLimit;
        })
    });

    $(document).on('click', '#add-role', function () {
        $.confirm({
            title: 'New role',
            Width: '80%',
            useBootstrap: false,
            closeOnclick: false,
            content: function () {
                var self = this;
                return $.ajax({
                    url: "<?php echo @route('getCreateNewRoleItem')?>",
                }).done(function (response) {
                    self.setContent(response);
                }).fail(function () {
                    self.setContent('');
                });
            },
            buttons: {
                Save: {
                    text: '<span class="glyphicon glyphicon-floppy-disk"></span> Submit',
                    btnClass: 'btn btn-primary',
                    action: function () {
                        saveNewRole(function(res){
                            console.log(res);
                            if(res.status == '{{\App\Core\Common\SDBStatusCode::OK}}'){
                                location.reload();
                            }else{
                                _commonShowError(res.data);
                            }
                        });
                        return false;
                    }
                },
                cancel: {
                    text: ' Close',
                    btnClass: 'btn btn-default',
                    action: function () {
                    }
                }
            }
        });

    });

    function saveNewRole(callback) {
        var form_action = $("#create-role").find("form").attr("action");
        var name = $("#create-role").find("input[name='name']").val();
        var role_value = $("#create-role").find("input[name='role_value']").val();
        var description = $("#create-role").find("input[name='description']").val();

        $.ajax({
            data: {name: name, role_value: role_value, description: description},
            type: 'post',
            dataType: 'json',
            url: form_action,
            success: function (response) {
                if (callback) {
                    callback(response);
                }
            },
            error: function (response) {
                if (callback) {
                    callback(response.responseJSON);
                }

            }
        });

    }

    function filter() {
        var role             = $("#cb-role").val();
        var module           = $("#cb-module").val();
        var controller       = $("#text-controller").val();
        var action           = $("#text-action").val();
        var limit            = $("#limit").val();
        var stringRole       = (role != "") ? "role=" + role : "";
        var stringModule     = (module != "") ? "&module=" + module : "";
        var stringController = (controller != "") ? "&controller=" + controller : "";
        var stringAction     = (action != "") ? "&action=" + action : "";
        var stringLimit      = (limit != "") ? "&limit=" + limit : "";
        window.location.search = stringRole+stringModule+stringController+stringAction+stringLimit;
    }

    //==================================function notify===========================
    function notify(headingContent, icon, content, bgColor, loaderBg) {
        $.toast({
                    text: content,
                    heading: headingContent,
                    icon: icon,
                    showHideTransition: 'plain',
                    allowToastClose: true,
                    hideAfter: 5000,
                    bgColor: bgColor,
                    stack: 5,
                    position: 'top-right',
                    textAlign: 'left',
                    loader: true,
                    loaderBg: loaderBg
                });
    }

</script>

@endsection
