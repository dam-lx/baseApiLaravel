<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/dev/layouts/style.css">
</head>
<body>
<div id="contents" class="main_only">
    <div class="container-fluid inner no-padding">
        <div id="main">
            <h1 class="page-heading">特定商取引法に基づく表記</h1>
            <div id="tradelaw_wrap" class="container-fluid">
                <div class="row">
                    <div id="tradelaw_box" class="col-md-10 col-md-offset-1">
                        <div id="tradelaw__body" class="dl_table">

                            @if($help->law_company)
                            <dl id="tradelaw__law_company">
                                <dt>販売業者</dt>
                                <dd>{{ $help->law_company }}</dd>
                            </dl>
                            @endif

                            @if( $help->law_manager)
                            <dl id="tradelaw__law_manager">
                                <dt>運営責任者</dt>
                                <dd>{{ $help->law_manager }}</dd>
                            </dl>
                            @endif

                            @if( $help->law_zip01)
                            <dl id="tradelaw__zip">
                                <dt>住所</dt>
                                <dd>〒{{ $help->law_zip01 }}-{{ $help->law_zip02 }}<br />
                                    {{ $help->law_pref}}{{ $help->law_addr01 }}{{ $help->law_addr02 }}
                                </dd>
                            </dl>
                            @endif

                            @if( $help->law_tel01)
                            <dl id="tradelaw__tel">
                                <dt>電話番号</dt>
                                <dd>{{ $help->law_tel01 }}-{{ $help->law_tel02 }}-{{ $help->law_tel03 }}</dd>
                            </dl>
                            @endif

                            @if(!empty($help->law_fax01))
                            <dl id="tradelaw__fax">
                                <dt>FAX番号</dt>
                                <dd>{{ $help->law_fax01 }}-{{ $help->law_fax02 }}-{{ $help->law_fax03 }}</dd>
                            </dl>
                            @endif

                            @if( $help->law_email)
                            <dl id="tradelaw__email">
                                <dt>メールアドレス</dt>
                                <dd><a href="mailto:{{ $help->law_email }}">{{ $help->law_email }}</a></dd>
                            </dl>
                            @endif

                            @if( $help->law_url)
                            <dl id="tradelaw__law_url">
                                <dt>URL</dt>
                                <dd><a href="{{ $help->law_url }}">{{ $help->law_url }}</a></dd>
                            </dl>
                            @endif

                            @if( $help->law_term01)
                            <dl id="tradelaw__law_term01">
                                <dt>商品以外の必要代金</dt>
                                <dd>{{ $help->law_term01 }}</dd>
                            </dl>
                            @endif

                            @if( $help->law_term02)
                            <dl id="tradelaw__law_term02">
                                <dt>注文方法</dt>
                                <dd>{{ $help->law_term02 }}</dd>
                            </dl>
                            @endif

                            @if( $help->law_term03)
                            <dl id="tradelaw__law_term03">
                                <dt>支払方法</dt>
                                <dd>{{ $help->law_term03 }}</dd>
                            </dl>
                            @endif

                            @if( $help->law_term04)
                            <dl id="tradelaw__law_term04">
                                <dt>支払期限</dt>
                                <dd>{{ $help->law_term04 }}</dd>
                            </dl>
                            @endif

                            @if( $help->law_term05)
                            <dl id="tradelaw__law_term05">
                                <dt>引渡し時期</dt>
                                <dd>{{ $help->law_term05 }}</dd>
                            </dl>
                            @endif

                            @if( $help->law_term06)
                            <dl id="tradelaw__law_term06">
                                <dt>返品・交換について</dt>
                                <dd>{{ $help->law_term06 }}</dd>
                            </dl>
                            @endif
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->

            </div>
        </div>
    </div>
</div>
</body>
</html>
