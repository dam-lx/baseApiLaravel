@extends("layouts.dev")
@push("css")
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dev/includes/userRole.css') }}">
@endpush

@section('content')
    @csrf

    <div class="row justify-content-center">
            <div class="card">
                <div class="card-header font-weight-bold">USER - ROLE</div>

                <div class="card-body form-group">
                    <fieldset>
                        <legend>Filter:</legend>
                        <div class="col-md-12 filter row">
                            <div class="col-md-12 form-group row">
                                <div class="col-md-2 form-title">Role</div>
                                <div class="col-md-4">
                                    <select id="cb_role" class="form-control">
                                        <option value="">---</option>
                                        <?php if(!empty($roleList)){?>
                                        <?php foreach ($roleList as $roleItem){?>
                                        $selected = "";
                                        if($item->user_role_value == $roleItem->role_value){
                                        $selected = "selected";
                                        }
                                        <option
                                            value="<?php echo $roleItem->name;?>"><?php echo $roleItem->name?></option>
                                        <?php   }
                                        }?>
                                    </select>
                                </div>
                                <div class="col-md-2 form-title">Active</div>
                                <div class="col-md-4">
                                    <select id="cb-active" class="form-control">
                                        <option value="">---</option>
                                        <option value="Actived">Actived</option>
                                        <option value="Not Active">Not Active</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 form-group row">
                                <div class="col-md-2 form-title">Name</div>
                                <div class="col-md-4">
                                    <input type="text" id="text-name" class="form-control"/>
                                </div>
                                <div class="col-md-2 form-title">Email</div>
                                <div class="col-md-4">
                                    <input type="text" id="text-email" class="form-control"/>

                                </div>
                            </div>
                        </div>


                    </fieldset>

                    <div class="table-responsive">
                    <table id="Useracl-table" class="table table-striped jambo_table table-hover table-user table-bordered">
                        <thead>
                        <tr class="headings">
                            <th class="column-title">###</th>
                            <th class="column-title">Email</th>
                            <th class="column-title">Name</th>
                            <th class="column-title">Gender</th>
                            <th class="column-title">Birthday</th>
                            <th class="column-title">IsActive</th>
                            <th class="column-title">Role</th>
                            <th class="display-none"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $index = 0; ?>
                        <?php if(!empty($dataUseRole)){
                        foreach ($dataUseRole as $item){
                        $index++;
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $index; ?></td>
                            <td><?php echo $item->user_email; ?></td>
                            <td><?php echo $item->user_name; ?></td>
                            <td>
                                <?php  if ($item->user_gender == 1) {
                                    echo 'nam';
                                }
                                elseif($item->user_gender == 2){
                                    echo 'nữ';
                                }
                                ?>
                            </td>

                            <td><?php echo $item->user_birth_date; ?></td>
                            <td>
                                <?php  if ($item->user_active == 1) {
                                    echo 'Actived';
                                }
                                else{
                                    echo 'Not active';
                                }
                                ?>
                            </td>
                            <td>


                                <select id="change-role" class="lang form-control" data-user-id="{{$item->user_id}}">
                                    <?php if(!empty($roleList)){?>
                                    <?php foreach ($roleList as $roleItem){
                                    $selected = "";
                                    if($item->user_role_value == $roleItem->role_value){
                                        $selected = "selected";
                                    }
                                    ?>
                                        <option value="<?php echo $roleItem->role_value ?>" {{$selected}}><?php echo $roleItem->name?>
                                    </option>
                                    <?php   }
                                    }?>
                                </select>

                            </td>
                            <td class="display-none">
                                {{$item->role_name}}
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
    </div>


@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#Useracl-table').DataTable(
                {
                    scrollY:'60vh',
                    scrollCollapse: true,
                    fixedHeader: true,
                    bJQueryUI: true,
                    info: false,
                    paging: false,
                    dom: 't',
                    searching: true,
                    "columnDefs": [{
                        "targets": 5,
                        "orderable": false,

                    },{
                        "type": "html-input", "targets": [6]
                    }]
                }
            );
            $('#text-name').on('change', function () {
                table.column(2).search(this.value).draw();
            });
            $('#cb_role').on('change', function () {
                table.column(7).search(this.value).draw();
            });
            $('#cb-active').on('change', function () {
                table.column(5).search(this.value).draw();
            });
            $('#text-email').on('change', function () {
                table.column(1).search(this.value).draw();
            });
            $(document).on('change', '#change-role', function () {
                var curren_id = $(this).data('user-id');
                var data = {
                    $current_id: curren_id,
                    $current_role_value: $(this).val()
                };
                $.ajax({
                    data: data,
                    type: 'Post',
                    dataType: 'json',
                    url: "<?php echo @route('updateUserRole')?>",
                    success: function (result) {
                        toastr.success('Update success!' ,{timeOut: 2000});
                    }
                });
            });
        });
    </script>

@endsection
