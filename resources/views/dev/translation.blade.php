@extends("layouts.dev")
@push("css")
    <link rel="stylesheet" type="text/css" href="{{asset('css/dev/includes/translation.css')}}">
@endpush

@section('content')
        <div class="row justify-content-center">
                <div class="card">
                    <div class="card-header font-weight-bold">Translation management form</div>
                    <fieldset>
                        <legend>Filter:</legend>
                        <div class="col-md-12 filter row">
                            <div class="col-md-12 form-group row">
                                <div class="col-md-2 form-title">Translate type</div>
                                <div class="col-md-3">
                                    <select id="trans-type" class="lang form-control">
                                        <option value="">---</option>
                                        <?php if(isset($dataComboFilter)&& count($dataComboFilter)>0) {?>
                                        <?php foreach ($dataComboFilter as $translateTypeItem){?>
                                        <option value="<?php echo $translateTypeItem->code;?>"><?php echo $translateTypeItem->code?></option>
                                        <?php   }
                                        }?>
                                    </select>
                                </div>
                                <div class="col-md-1"><button id="add-translate-type" class="btn btn-primary pull-left fa fa-plus"
                        title="Add new translate type"></button></div>
                            </div>
                            <div class="col-md-12 form-group row">
                                <div class="col-md-2 form-title">Text Code</div>
                                <div class="col-md-3">
                                    <input type="text" id="trans-text-code" class="form-control"/>
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-2 form-title">Text Translated</div>
                                <div class="col-md-4">
                                    <input type="text" id="trans-text-translated" class="form-control"/>
                                </div>
                            </div>
                        </div>


                    </fieldset>

                    <div class="function">
                        <button id="add" class="btn btn-primary pull-left fa fa-plus" title="Add new text"></button>
                        <button id="generation" class="btn btn-primary pull-right fa fa-download" title="Generate to translate file"></button>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="tbl-trans" class="table table-striped jambo_table table-hover table-user table-bordered">
                            <thead>
                                <tr class="headings">
                                    <th class="column-title">No</th>
                                    <th class="column-title">File</th>
                                    <th class="column-title">Key</th>
                                    <th style="min-width: 408px;" class="column-title">Text translated</th>
                                    <th class="column-title">
                                        {{--<span id="edit-all" class="glyphicon glyphicon-edit btn"></span>
                                        <span id="save-all" class="glyphicon glyphicon-floppy-saved btn display-none"></span>--}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $index = 0; ?>
                            <?php if(!empty($dataTrans)){
                            foreach ($dataTrans as $key=>$item){
                            $index++;
                            $typeOfTrans =  $item['key_list'][0];
                            $code = $item['key_string'];
                            ?>
                            <tr class="trans-record">
                                <td class="text-center"><?php echo $index; ?></td>
                                <td><?php echo $typeOfTrans; ?></td>
                                <td><?php echo $code; ?></td>
                                <td>
                                <?php
                                    foreach ($item['data'] as $langKey=>$itemData){//language ?>
                                    <div class="lang-input">
                                        <div class="lang-code"><?php echo $langKey; ?></div>
                                        <span style="display:none;"><?php echo $itemData; ?></span><textarea class="form-control text-trans" value="<?php echo $itemData; ?>" readonly /><?php echo $itemData; ?></textarea>
                                        <span class="edit fa fa-pencil-square-o"></span>
                                        <span class="save fa fa-save display-none" data-lang="{{$langKey}}" data-code="{{$code}}"></span>
                                    </div>
                                    <?php } ?>
                                </td>
                                <td>
                                        <span class="delete fa fa-trash-o" data-lang="{{$langKey}}" data-code={{$code}}></span>
                                </td>
                            </tr>
                            <?php
                                }
                            }?>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>

@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#tbl-trans').DataTable(
                {
                    /*scrollY:        '60vh',*/
                    scrollCollapse: true,
                    fixedHeader: true,
                    bJQueryUI: true,
                    info:     false,
                    paging: false,
                    dom: 't',
                    searching: true,
                    "columnDefs": [ {
                        "targets": 4,
                        "orderable": false
                    } ]
                }
            );

            $('#trans-type').on( 'change', function () {
                table.column(1).search( this.value ).draw();
            } );
            $('#trans-text-code').on( 'change', function () {
                table.column(2).search( this.value ).draw();
            } );
            $('#trans-text-translated').on( 'change', function () {
                table.column(3).search( this.value ).draw();
            } );
            $(document).on('click', '.edit', function () {
                var record = $(this).parents('.lang-input');
                $(record).find('.text-trans').prop('readonly', false).select();
                $(record).find('.save').removeClass('display-none');
                $(this).addClass('display-none');
            });
            /*$(document).on('click', '#edit-all', function () {

                if($(this).hasClass('open')){
                    $(this).removeClass('open');
                    $('.text-trans').prop('readonly', true);
                    $('.save').addClass('display-none');
                    $('.edit').removeClass('display-none');
                }else{
                    $(this).addClass('open');
                    $('.text-trans').prop('readonly', false);
                    $('.save').removeClass('display-none');
                    $('.edit').addClass('display-none');
                }
            });*/
            $(document).on('click', '.save', function () {
                var record = $(this).parents('.lang-input');
                var text = $(record).find('.text-trans').val();
                var lang = $(this).attr('data-lang');
                var code = $(this).attr('data-code');
                var data = {
                    lang: lang,
                    code:code,
                    text: text
                };

                $.ajax({
                    method: 'Post',
                    data: data,
                    url: "<?php echo @route('updateTranslate')?>",
                    success: function (result) {
                        if(result.status == '{{\App\Core\Common\SDBStatusCode::OK}}'){
                            $(record).find('.save').addClass('display-none');
                            $(record).find('.edit').removeClass('display-none');
                            $(record).find('.text-trans').prop('readonly', true);
                            clearError($(record).find('.text-trans'));
                        }else{
                            $(record).find('.text-trans').addClass('input-error');
                            var messageError =  result.message;
                            showError($(record).find('.text-trans'),messageError);
                            $(record).find('.text-trans').tooltip();
                        }
                    }
                });
            });
            $(document).on('click', '#generation', function () {
                $.ajax({
                    method: 'Post',
                    url: "<?php echo @route('generationLanguageFiles')?>",
                    success: function (result) {
                        alert('OK');
                    }
                });
            });

            // Delete Trans
            $(document).on('click', '.delete', function () {
                var lang = $(this).attr('data-lang');
                var code = $(this).attr('data-code');
                var record =  $(this).parents('.trans-record');
                $.confirm({
                    title: '<p class="text-warning">Warning</p>',
                    boxWidth: '500px',
                    useBootstrap: false,
                    closeOnclick: false,
                    content: "Are you want to delete all? It will delete all language with this code",
                    buttons: {
                        Save: {
                            text: 'OK',
                            btnClass: 'btn btn-primary',
                            action: function () {
                                $.ajax({
                                    method: 'Post',
                                    data:{code:code,lang:lang},
                                    url: "<?php echo @route('deleteTranslate')?>",
                                    success: function (result) {
                                        if(result.status=='{{\App\Core\Common\SDBStatusCode::OK}}'){
                                           $(record).remove();
                                        }else{
                                            alert("Error :"+result.message);
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

            $(document).on('click', '#add', function () {
                $.confirm({
                    title: 'New text translate',
                    Width: '80%',
                    useBootstrap: false,
                    closeOnclick: false,
                    content: function () {
                        var self = this;
                        return $.ajax({
                            url: "<?php echo @route('newTextTrans')?>",
                        }).done(function (response) {
                            self.setContent(response);
                        }).fail(function () {
                            self.setContent('');
                        });
                    },
                    buttons: {
                        Save: {
                            text: '<span class="glyphicon glyphicon-floppy-disk"></span> Save',
                            btnClass: 'btn btn-primary',
                            action: function () {
                                saveNewTranslateText(this.$content,function(res){
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
                            text: ' Cancel',
                            btnClass: 'btn btn-default',
                            action: function () {
                            }
                        }
                    }


                });

            });

        });
        function saveNewTranslateText(popupContent,callback){
            var textTrans = {};
            $(popupContent).find('.trans-text').each(function () {
                textTrans[$(this).data('lang')] = $(this).val();
            });
            var data =
            {
                _token: $('meta[name="csrf-token"]').attr('content'),
                trans_type:$(popupContent).find('#trans-type').val(),
                text_code:$(popupContent).find('#trans-code').val(),
                text_trans:JSON.stringify(textTrans)
            };
           $.ajax({
                data:data,
                type:'post',
                dataType:'json',
                url: "<?php  echo @route('createNewTranslationItem')?>",
                success: function (response) {
                    if(callback){
                        callback(response);
                    }
                },
               error:function(response){
                   if(callback){
                       callback(response.responseJSON);
                   }

               }
            });
        }
        function clearError(input){
            $(input).removeClass('input-error');
            $(input).removeAttr('data-original-title');
        }
        function showError(input,message){
            $(input).addClass('input-error');
            $(input).attr('data-original-title',message);
        }


$(document).on('click', '#add-translate-type', function () {
        $.confirm({
            title: 'New translate type',
            Width: '80%',
            useBootstrap: false,
            closeOnclick: false,
            content: function () {
                var self = this;
                return $.ajax({
                    url: "<?php echo @route('getCreateNewTranslateType')?>",
                }).done(function (response) {
                    self.setContent(response);
                }).fail(function () {
                    self.setContent('');
                });
            },
            buttons: {
                Save: {
                    text: '<span class="fa fa-save"></span> Submit',
                    btnClass: 'btn btn-primary',
                    action: function () {
                        saveNewTranslateType(function(res){
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

    function saveNewTranslateType(callback){
            var form_action = $("#create-translate-type").find("form").attr("action");
            var code = $("#create-translate-type").find("input[name='code']").val();
            var order_value = $("#create-translate-type").find("input[name='order_value']").val();
            var comment = $("#create-translate-type").find("input[name='comment']").val();

            $.ajax({
                data:{code:code, order_value:order_value, comment:comment},
                type:'post',
                dataType:'json',
                url: form_action,
                success: function (response) {
                    if(callback){
                        callback(response);
                    }
                },
               error:function(response){
                   if(callback){
                       callback(response.responseJSON);
                   }

               }
            });

        }

    </script>

@endsection
