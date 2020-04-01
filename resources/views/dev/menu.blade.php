@extends("layouts.dev")
@push("css")
    <link href="{{asset('css/dev/includes/menu.css')}}" rel="stylesheet">
@endpush
<!-- Latest compiled and minified CSS & JS -->
@section('content')
    <div class="row justify-content-center">
        <div class="card">
            <div class="card-header font-weight-bold">Menu</div>
            <h4>Basic</h4>
            <div class="card-body form-group basic-menu">
                <ul class="basic">
                    <?php
                    $prevLevel = 0;
                    $count = count($dataCategory);
                    for($i = 0;$i < $count ; $i++){
                    if($dataCategory[$i]->level_value == $prevLevel){?>

                    <li class="menu-item row" data-id="<?php echo $dataCategory[$i]->id; ?>" >
                        <?php if ($i+1 < $count && $dataCategory[$i+1]->level_value > $dataCategory[$i]->level_value){
                            $isHide = '';
                        }else{
                            $isHide = 'display-none';
                        }?>
                        <span class="fa fa-plus plusButton  {{ $isHide }}"></span>
                        <span class="fa fa-minus minusButton fa-minus {{ $isHide }}"></span>
                        <div class=" dInput dInput-header col-lg-10 col-md-9 col-sm-12">
                            <input type="text" class="CssName menuName form-control " disabled value="<?php echo $dataCategory[$i]->name; ?>" placeholder="New Name">
                            <span class="fa checkName checked fa-check"></span>
                            <span class="fa attach fa-paperclip"></span>
                            <input type="text" class=" form-control menuURL CssURL" disabled value="<?php echo $dataCategory[$i]->url; ?>" placeholder="http://">
                            <span class="fa checkURL checked fa-check"></span>
                        </div>

                        <div class="CUD ButtonCUD ButtonCUD-header col-lg-2 col-md-3 col-sm-12">
                            <a class="pull-right btn btn-danger btn-sm itemDelete delete btn-xs"><span class="fa fa-times-circle"></span></a>
                            <a class="pull-right btn btn-warning btn-sm itemEdit edit btn-xs" ><span class="fa fa-pencil-square-o"></span></a>
                            <a class="pull-right btn btn-info update btn-sm itemUpdate btn-xs" hidden="true"><span class="fa fa-save"></span></a>
                            <a class="pull-right btn btn-primary btn-sm iteamCreate create btn-xs" ><span class="fa fa-plus"></span></a>
                        </div>
                        <?php if ($i+1 < $count && $dataCategory[$i+1]->level_value > $dataCategory[$i]->level_value){?>
                            <?php }else{ ?>
                    </li>
                    <?php } ?>
                    <?php }else if($dataCategory[$i]->level_value > $prevLevel){?>

                    <ul class="group-menu-item display-none">
                        <li class="menu-item row" data-id="<?php echo $dataCategory[$i]->id; ?>">
                            <?php if ($i+1 < $count && $dataCategory[$i+1]->level_value > $dataCategory[$i]->level_value){
                                $isHide = '';
                            }else{
                                $isHide = 'display-none';
                            }?>
                            <span class="fa plus plusButton fa-plus {{ $isHide }}"></span>
                            <span class="fa minus minusButton fa-minus {{ $isHide }}"></span>
                            <div class=" dInput">
                                <input type="text" class="CssName menuName form-control" disabled value="<?php echo $dataCategory[$i]->name; ?>" placeholder="New Name">
                                <span class="fa checkName checked fa-check"></span>
                                <span class="fa attach fa-paperclip"></span>
                                <input type="text" class=" form-control menuURL CssURL" disabled value="<?php echo $dataCategory[$i]->url; ?>" placeholder="http://">
                                <span class="fa checkURL checked fa-check"></span>

                            </div>

                            <div class="CUD col-lg-2 col-md-3 col-sm-12">
                                <a class="pull-right btn btn-danger itemDelete delete btn-xs"><span class="fa fa-remove"></span></a>
                                <a class="pull-right btn btn-warning itemEdit edit btn-xs" ><span class="fa fa-edit"></span></a>
                                <a class="pull-right btn btn-info update itemUpdate btn-xs" hidden="true"><span class="fa fa-save"></span></a>
                                <a class="pull-right btn btn-primary iteamCreate create btn-xs" ><span class="fa fa-plus"></span></a>
                            </div>
                            <?php if ($i+1 < $count && $dataCategory[$i+1]->level_value > $dataCategory[$i]->level_value){?>

                            <?php }else{ ?>
                        </li>
                        <?php } ?>
                        <?php }else{?>
                        <?php for($j = $dataCategory[$i]->level_value;$j<$prevLevel;$j++){ ?>
                    </ul>
                    <?php } ?>
                    <li class="menu-item row" data-id="<?php echo $dataCategory[$i]->id; ?>">
                        <?php if ($i+1 < $count && $dataCategory[$i+1]->level_value > $dataCategory[$i]->level_value){
                            $isHide = '';
                        }
                        else{
                            $isHide = 'display-none';
                        }?>
                        <span class="fa plus plusButton fa-plus {{ $isHide }}"></span>
                        <span class="fa minus minusButton fa-minus {{ $isHide }}"></span>
                        <div class=" dInput">
                            <input type="text" class="CssName menuName form-control" disabled value="<?php echo $dataCategory[$i]->name; ?>" placeholder="New Name">
                            <span class="fa checked checkName fa-check"></span>
                            <span class="fa attach fa-paperclip"></span>
                            <input type="text" class=" form-control menuURL CssURL" disabled value="<?php echo $dataCategory[$i]->url; ?>" placeholder="http://">
                            <span class="fa checked checkURL fa-check"></span>

                        </div>
                        <div class="CUD col-lg-2 col-md-3 col-sm-12">
                            <a class="pull-right btn btn-danger itemDelete delete btn-xs"><span class="fa fa-remove"></span></a>
                            <a class="pull-right btn btn-warning itemEdit edit btn-xs" ><span class="fa fa-pencil"></span></a>
                            <a class="pull-right btn btn-info update itemUpdate btn-xs" hidden="true"><span class="fa fa-save"></span></a>
                            <a class="pull-right btn btn-primary iteamCreate create btn-xs" ><span class="fa fa-plus"></span></a>
                        </div>
                        <?php if ($i+1 < $count && $dataCategory[$i+1]->level_value > $dataCategory[$i]->level_value){?>
                            <?php }else{ ?>
                    </li>
                    <?php } ?>
                    <?php }?>
                    <?php
                    $prevLevel = $dataCategory[$i]->level_value;
                    }?>
                </ul>
            </div>
            <div class="ref">
                <b>Refer: </b><a href="http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/" target="_blank">http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/</a>
            </div>
        </div>
    </div>


    <div id="new-node-temp" hidden="true">
        <li class="menu-item row">
            <span class="fa plus plusChid plusButton fa-plus"></span>
            <span class="fa minus minusButton fa-minus"></span>
            <div class="dInput col-lg-10 col-md-9 col-sm-12">
                <input type="text" class="newNodeName menuName CssName form-control " placeholder="New Name" />
                <span class="fa checkName checked fa-check"></span>
                <span class="fa fa-paperclip attach"></span>
                <input type="text" class=" form-control newNodeURL menuURL CssURL" placeholder="http://">
                <span class="fa checkURL checked fa-check"></span>

            </div>
            <div class="CUD col-lg-2 col-md-3 col-sm-12" >
                <a class="pull-right btn btn-info save itemSave btn-xs" ><span class="fa fa-save"></span></a>
                <a class="pull-right btn btn-danger itemDelete delete btn-xs"><span class="fa  fa-times-circle"></span></a>
                <a class="pull-right btn btn-warning edit itemEdit editchid btn-xs"><span class="fa fa-pencil"></span></a>
                <a class="pull-right btn btn-info itemUpdate update btn-xs"><span class="fa fa-save"></span></a>
                <a class="pull-right btn btn-primary iteamCreate create createChid btn-xs" ><span class="fa fa-plus"></span></a>
            </div>
        </li>
    </div>

    <div id="new-node-group-temp" hidden="true">
        <ul class="group-menu-item">
            <li class="menu-item row">
                <span class="fa plus plusChid plusButton fa-plus"></span>
                <span class="fa minus minusButton fa-minus"></span>
                <div class="dInput col-lg-10 col-md-9 col-sm-12">
                    <input type="text" class="newNodeName menuName CssName form-control " placeholder="New Name" />
                    <span class="fa checkName checked fa-check"></span>
                    <span class="fa fa-paperclip attach"></span>
                    <input type="text" class=" form-control newNodeURL menuURL CssURL" placeholder="http://">
                    <span class="fa  checked fa-check"></span>

                </div>
                <div class="CUD col-lg-2 col-md-3 col-sm-12">
                    <a class="pull-right btn btn-info btn-sm itemSave save btn-xs"><span class="fa fa-save"></span></a>
                    <a class="pull-right btn btn-danger btn-sm itemDelete delete btn-xs"><span class="fa fa-times-circle"></span></a>
                    <a class="pull-right btn btn-warning btn-sm itemEdit edit editchid btn-xs"><span class="fa fa-pencil"></span></a>
                    <a class="pull-right btn btn-info btn-sm itemUpdate update btn-xs" ><span class="fa fa-save"></span></a>
                    <a class="pull-right btn btn-primary btn-sm iteamCreate create createChid btn-xs" ><span class="fa fa-plus"></span></a>
                </div>
            </li>
        </ul>
    </div>


@endsection
@section('scripts')

    <script type="text/javascript">

        $('.update').addClass('display-none');
        $('.updatechid').addClass('display-none');
        $('.minus').addClass('display-none');
        $('.createChid').addClass('display-none');
        $('.editchid').addClass('display-none');
        $('.plusChid').addClass('display-none');




        $(document).ready(function () {
            $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

// Show menu
            $(document).on('click', '.plusButton' ,function(event) {
                $(this).parent('.menu-item').find('ul.group-menu-item').first().removeClass('display-none');
                $(this).next('.minusButton').removeClass('display-none');
                $(this).addClass('display-none');
            });


// Hide Menu
            $(document).on('click', '.minusButton' ,function(event) {
                $(this).parent('.menu-item').find('ul.group-menu-item').first().addClass('display-none');
                $(this).prev('.plusButton').removeClass('display-none');
                $(this).addClass('display-none');
            });

//Create New menu
            $(document).on('click', '.create' ,function(event) {
                var currentNode = $(this).parents('li.menu-item').first();
                var parentNodeId =  $(this).parents('li.menu-item').data('id');
                var addNode = $('#new-node-temp').clone().contents();
                var addNodeGroup = $('#new-node-group-temp').clone().contents();
                var childBag = $(this).parent('li.menu-item').nextAll('ul.group-menu-item').first();

                $(this).removeClass('display-none');
                $(currentNode).find('.plusButton').first().trigger('click');

                if(childBag.length>0){
                    $(addNode).find('input.newNodeName').first().attr('parentNodeId',parentNodeId);
                    $(addNode).removeClass('display-none');
                    $(this).parents('li.menu-item').first().find('ul.group-menu-item').first().prepend(addNode);
                    $(this).parents('li.menu-item').first().find('input.newNodeName').first().focus();

                    if($(currentNode).find('li.menu-item').length>0){
                        $(currentNode).find('.minusButton').first().removeClass('display-none');
                    }
                }else{
                    $(addNodeGroup).find('input.newNodeName').first().attr('parentNodeId',parentNodeId);
                    $(addNodeGroup).removeClass('display-none');
                    $(this).parents('li.menu-item').first().append(addNodeGroup);
                    $(this).parents('li.menu-item').first().find('input.newNodeName').first().focus();
                    // $(this).parents('li.menu-item').first().find('input.newNodeURL').first().focus();
                    if($(currentNode).find('li.menu-item').length>0){
                        $(currentNode).find('.minusButton').first().removeClass('display-none');
                    }
                }
            });

//Save
            $(document).on('click','.save', function(event) {

                var parentAddId =  $(this).parents('li.menu-item').first().data('id');

                var liParent = $(this).parents('li.menu-item').first();
                var insert = $(this);
                var input = $(this).parents('li.menu-item').first().find('input.menuName').first();
                var inputURL = $(this).parents('li.menu-item').first().find('input.newNodeURL').first();
                var name = $(input).val();
                var url = $(inputURL).val();

                var parentId = $(input).attr('parentNodeId');

                var currentNode =$(this).parents('li.menu-item').first();
                var parentNode  =$(currentNode).parents('li.menu-item').first();
                var datas = {
                    url:url,
                    name:name,
                    parent_id:parentId
                };

                if(validateMenu($(input))==true) {

                    $(liParent).find('.edit').first().removeClass('display-none');
                    $(liParent).find('.save').first().addClass('display-none');
                    $(liParent).find('.create').first().removeClass('display-none');

                    $(input).prop('disabled', true);
                    $(inputURL).prop('disabled', true);
                    $.ajax({
                               url: '{{ route('createMenu') }}',
                               type: 'POST',
                               dataType: 'JSON',
                               data: datas,
                               success: function (res) {
                                   var dataFromSP = JSON.parse(res.data[0].data);
                                   var newId = dataFromSP.newid;
                                   $(insert).parents('ul.group-menu-item').first().find('li.menu-item').first().attr('data-id', newId);
                                   toastr.success('Create new success!', '',{timeOut: 2000});

                               }
                           });
                    clearError($(input));
                }
                else{

                    showMenuError($(input));

                }
            });

// Edit Enabled input
            $(document).on('click', '.edit' ,function(event) {
                var liParent =  $(this).parents('li.menu-item').first();
                $(this).next("a").removeAttr('hidden');
                $(liParent).find('input.menuName').first().prop('disabled', false).select();
                $(liParent).find('input.menuURL').first().prop('disabled', false).select();
                $(liParent).find('.edit').first().addClass('display-none');
                $(liParent).find('.create').first().addClass('display-none');
                $(liParent).find('.update').first().removeClass('display-none');

            });

// Update
            $(document).on('click','.update', function(event) {

                var curren = $(this);

                var id = $(this).parents('li.menu-item').data('id');
                var input = $(this).parents('li.menu-item').first().find('input.menuName').first();
                var inputURL = $(this).parents('li.menu-item').first().find('input.menuURL').first();
                var name = $(input).val();
                var url = $(inputURL).val();
                var liParent = $(this).parents('li.menu-item').first();

                var data = {
                    id:id,
                    name:name,
                    url:url
                };

                if(validateMenu($(input))==true){

                    $(input).prop('disabled', true);
                    $(inputURL).prop('disabled', true);
                    $(liParent).find('.update').first().addClass('display-none');
                    $(liParent).find('.edit').first().removeClass('display-none');
                    $(liParent).find('.create').first().removeClass('display-none');
                    $.ajax({
                               url: '{{ route('updateMenu') }}',
                               type: 'POST',
                               dataType: 'JSON',
                               data: data,
                               success: function (res) {
                                   toastr.success('Update success!', '',{timeOut: 2000});
                               }
                           });
                    clearError($(input));
                    validateMenu($(input));
                }
                else{
                    showMenuError($(input));
                }
            });




//Delete
            $(document).on('click','.delete', function(event) {
                var id = $(this).parents('li.menu-item').data('id');
                var currentNode = $(this).parents('li.menu-item').first();
                var parentNode  = $(currentNode).parents('li.menu-item').first();
                var buttonDelete =  $(this);
                if (id){
                    $.confirm({
                                  title: '!!!!!!!',
                                  content: 'YOU SURE WANT TO DELETE ?',
                                  type: 'red',
                                  typeAnimated: true,

                                  buttons: {
                                      deleteUser: {
                                          btnClass: 'btn-red',
                                          text: 'Yes,I agree',
                                          action: function () {
                                              $.ajax({
                                                         url: '{{ route('deleteMenu') }}',
                                                         type: 'DELETE',
                                                         data: {id: id},
                                                         success : function(res) {
                                                             if (res.status) {
                                                                 $(currentNode).remove();
                                                                 if ($(parentNode).find('li.menu-item').length <=0){
                                                                     $(parentNode).find('.minusButton').first().addClass('display-none');

                                                                 }
                                                                 toastr.success('Deleted success!', '',{timeOut: 2000});

                                                             }
                                                         }
                                                     });
                                          }
                                      },
                                      Cancel: function () {
                                      }
                                  }
                              });
                }
                else{
                    $(currentNode).remove();
                    if ($(parentNode).find('li.menu-item').length <=0){
                        $(parentNode).find('.minusButton').first().addClass('display-none');
                    }
                }

            });

            // var selector = $(this).parents('li.menu-item').first().find('input.menuName').first();
            function showMenuError(selector){
                var messageError ="This field is not empty !";
                $(selector).attr('data-original-title',messageError);
                $(selector).tooltip('show');
                $(selector).addClass('input-error');
            }
            function clearError(selector){
                if(selector.length > 0){
                    $(selector).attr('data-original-title','');
                    $(selector).removeClass('input-error');
                }else{
                    $('.input-error').each(function(){
                        $(selector).attr('data-original-title','');
                        $(selector).removeClass('input-error');
                    });
                }
            }

            function validateMenu(selector) {
                var result =  false;
                //check required
                if($(selector).val()!=''){
                    result =  true;
                }
                return result;
            }
        });
    </script>


@endsection
