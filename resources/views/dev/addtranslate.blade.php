@extends("layouts.dev")

@section('content')
    <style>
        .form-control {
            height: 35px !important;
        }

        .form-row {
            margin-top: 5px;
        }
    </style>
    <div class="justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body form-group">
                    <div class="col-md-12 form-row">
                        <div class="col-md-3 form-title">Translate type</div>
                        <div class="col-md-4">
                            <select id="trans-type" class="form-control">
                                <?php if(isset($comboList) && count($comboList)>0){?>
                                <?php foreach ($comboList as $transType){?>
                                <option value="<?php echo $transType->code;?>"><?php echo $transType->code;?></option>
                                <?php   }
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 form-row">
                        <div class="col-md-3 form-title">Code</div>
                        <div class="col-md-9">
                            <input type="text" id="trans-code" name="text_code" class="form-control"/>
                        </div>
                    </div>
                    <?php if(isset($langList) && count($langList)>0){?>
                    <?php foreach ($langList as $langItem){?>
                    <div class="col-md-12 form-row">
                        <div class="col-md-3 form-title">Text translate to <?php echo $langItem->name;?></div>
                        <div class="col-md-9">
                            <textarea type="text" data-lang="<?php echo $langItem->code;?>" id="trans-text-<?php echo $langItem->code;?>" class="form-control trans-text"></textarea>
                        </div>
                    </div>
                    <?php   }
                    }?>
                </div>
            </div>
        </div>
    </div>

@endsection
