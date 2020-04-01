<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/dev/layouts/style.css">
</head>
<body>
<div id="contents" class="main_only mt-5">
    <div class="container-fluid inner no-padding">
        <div id="main">
            <h1 class="page-heading">当サイトについて</h1>
            <div id="help_about" class="container-fluid">
                <div id="help_about_box" class="row">
                    <div id="help_about_box__body" class="col-md-10 col-md-offset-1">
                        <div id="help_about_box__body_innner" class="dl_table">
                            @if($baseInfo->shop_name)
                            <dl id="help_about_box__shop_name">
                                <dt>店名</dt>
                                <dd>{{ $baseInfo->shop_name }}</dd>
                            </dl>
                            @endif

                            @if($baseInfo->company_name)
                            <dl id="help_about_box__company_name">
                                <dt>会社名</dt>
                                <dd>{{ $baseInfo->company_name }}</dd>
                            </dl>
                            @endif

                            @if($baseInfo->zip01)
                            <dl id="help_about_box__zip">
                                <dt>所在地</dt>
                                <dd>〒{{ $baseInfo->zip01 }}-{{ $baseInfo->zip02 }}<br />
                                    {{ $baseInfo->pref }}{{ $baseInfo->addr01 }}{{ $baseInfo->addr02 }}
                                </dd>
                            </dl>
                            @endif

                            @if($baseInfo->tel01)
                            <dl id="help_about_box__tel">
                                <dt>電話番号</dt>
                                <dd>{{ $baseInfo->tel01 }}-{{ $baseInfo->tel02 }}-{{ $baseInfo->tel03 }}</dd>
                            </dl>
                            @endif

                            @if($baseInfo->fax01)
                            <dl id="help_about_box__fax">
                                <dt>FAX番号</dt>
                                <dd>{{ $baseInfo->fax01 }}-{{ $baseInfo->fax02 }}-{{ $baseInfo->fax03 }}</dd>
                            </dl>
                            @endif

                            @if($baseInfo->business_hour)
                            <dl id="help_about_box__business_hour">
                                <dt>営業時間</dt>
                                <dd>{{ $baseInfo->business_hour }}</dd>
                            </dl>
                            @endif

                            @if($baseInfo->good_traded)
                            <dl id="help_about_box__good_traded">
                                <dt>取扱商品</dt>
                                <dd>{{ $baseInfo->good_traded }}</dd>
                            </dl>
                            @endif

                            @if($baseInfo->message)
                            <dl id="help_about_box__message">
                                <dt>メッセージ</dt>
                                <dd>{{ $baseInfo->message }}</dd>
                            </dl>
                            @endif
                        </div>

                        <div id="maps"></div>

                    </div><!-- /.col -->
                </div><!-- /.row -->

            </div>
        </div>
    </div>
</div>
</body>
</html>
