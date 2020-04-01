<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/dev/layouts/style.css">
</head>
<body>
<div id="contents" class="main_only">
    <div id="privacy_wrap" class="container-fluid inner no-padding">
        <div id="main">
            <h1 class="page-heading">プライバシーポリシー</h1>
            <div id="privacy_box" class="container-fluid">
                <div id="privacy_box__body" class="row">
                    <div id="privacy_box__body_inner" class="col-md-10 col-md-offset-1">
                        <p id="privacy_box__declaration">
                            @if($baseInfo->company_name)
                            {{ $baseInfo->company_name }}は、
                            @endif
                            個人情報保護の重要性に鑑み、「個人情報の保護に関する法律」及び本プライバシーポリシーを遵守し、お客さまのプライバシー保護に努めます。
                        </p>
                        <br />
                        <h3 id="privacy_box__lead_header">個人情報の定義</h3>
                        <p id="privacy_box__lead">お客さま個人に関する情報(以下「個人情報」といいます)であって、お客さまのお名前、住所、電話番号など当該お客さま個人を識別することができる情報をさします。他の情報と組み合わせて照合することにより個人を識別することができる情報も含まれます。</p>
                    </div><!-- /.col -->
                </div><!-- /.row -->

            </div>
        </div>
    </div>
</div>
</body>
</html>
