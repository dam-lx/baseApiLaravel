@extends("layouts.dev")
@push("css")
    <link href="{{asset('css/dev/includes/role.css')}}" rel="stylesheet"/>
@endpush

@section('content')

    <div class="row justify-content-center">
            <div class="card">
                <div class="card-header font-weight-bold">Roles management form</div>
                <div class="function">
                    <button id="add" class="btn btn-primary pull-left fa fa-plus" title="Add new text"></button>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbl-roles" class="table table-striped jambo_table table-hover table-user table-bordered">
                        <thead>
                        <tr class="headings">
                            <th class="column-title">No</th>
                            <th class="column-title">Name</th>
                            <th class="column-title">Roles Value</th>
                            <th style="min-width: 408px;" class="column-title">Description</th>
                            <th class="column-title">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $index = 0; ?>
                        <?php if(!empty($roles)){
                        foreach ($roles as $item){
                        $index++;
                        ?>
                        <tr class="roles-record">
                            <td class="text-center"><?php echo $index; ?></td>
                            <td><?php echo $item->name; ?></td>
                            <td><?php echo $item->role_value; ?></td>
                            <td><?php echo $item->description; ?></td>
                            <td class="text-center">
                                <span class="edit-role fa fa-pencil-square-o"
                                      data-id="<?php echo $item->id; ?>"></span>
                                <span class="delete-role fa fa-trash-o" data-id="{{$item->id}}"></span>
                            </td>
                        </tr>
                        <?php }
                        }?>
                        </tbody>
                    </table>
                    </div>

                </div>
            </div>
    </div>
    <div id="modal-iFrame" class="iziModal" display="none"></div>
    <div id="modal-iFrame2" display="none"></div>




@endsection
@push("js")
    <script src="{{asset('js/dev/includes/role.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            dataTable();
        });

        $.ajaxSetup({

                        headers: {

                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                        }
                    });


        //create role
        $(document).on('click', '#add', function () {
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

        ///Edit role

        $(document).on('click', '.edit-role', function (event) {
            event.preventDefault();
            $('#modal-iFrame2').iziModal('open', this); // Do not forget the "this"
        });

        $("#modal-iFrame2").iziModal({
                                         title: 'Edit Role', //Modal title
                                         subtitle: 'Fill the table.', //Modal subtitle
                                         headerColor: 'rgb(51, 76, 123)', //Color of modal header. Hexa colors allowed.
                                         overlayColor: 'rgba(0, 0, 0, 0.4)', //Color of overlay behind the modal
                                         iconColor: '',
                                         iconClass: 'icon-chat',
                                         iframe: true, //In this example, this flag is mandatory. Izimodal needs to understand you will call an iFrame from here
                                         iframeURL: "", //Link will be opened inside modal
                                         onOpening: function (modal) {

                                             var id = $(event.target).closest("span").data("id");//get Id, get button then get id
                                             $(".iziModal-iframe").attr("src", "{{route('getEditRoleItem')}}?id=" + id);
                                         },

                                     });


        $(document).on('click', '.delete-role', function () {
            var id = $(this).data('id');
            var code = $(this).data("code");
            $.confirm({
                          title: '<p class="text-warning">Warning</p>',
                          boxWidth: '500px',
                          useBootstrap: false,
                          closeOnclick: false,
                          content: "Are you sure to delete?",
                          buttons: {
                              Save: {
                                  text: 'OK',
                                  btnClass: 'btn btn-primary',
                                  action: function () {
                                      $.ajax({
                                                 method: 'Post',
                                                 data: {code: code},
                                                 url: '{{route('deleteRole')}}?id=' + id,
                                                 success: function (result) {
                                                     if(result.status == '{{\App\Core\Common\SDBStatusCode::OK}}'){
                                                         location.reload();
                                                     }else{
                                                         alert(result.message);
                                                     }
                                                 }
                                             });
                                  }
                              },
                              cancel: {
                                  text: ' Cancel',
                                  btnClass: 'btn btn-danger',
                                  action: function () {
                                  }
                              }
                          }
                      });
        });


        function dataTable() {
            var table = $('#tbl-roles').DataTable(
                {
                    scrollY: '60vh',
                    scrollCollapse: true,
                    fixedHeader: true,
                    bJQueryUI: true,
                    info: false,
                    paging: false,
                    dom: 't',
                    searching: false
                }
            );
        }
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
    </script>
@endpush
