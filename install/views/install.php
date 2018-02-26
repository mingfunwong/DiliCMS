<?php if (!defined('IN_CMS')) exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="zh-cn" class="fuelux">
<head>
    <meta charset="utf-8">
    <title>安装程序</title>
    <base href="<?php echo base_url(); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="安装程序">
    <meta name="author" content="Cms">
    <link href="static/js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="static/js/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="static/js/fuelux/css/fuelux.min.css" rel="stylesheet">
    <link href="static/js/fuelux/css/fuelux-responsive.min.css" rel="stylesheet">
    <link href="static/css/install.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="static/js/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="row">
        <div class="span12 columns">
            <div class="well" style="position: relative">
                <div class="wizard" id="installWizard">
                    <ul class="steps">
                        <li data-target="#step-license" class="active">
                            <span class="badge badge-info">1</span>关于CMS<span class="chevron"></span>
                        </li>
                        <li data-target="#step-platform" class="">
                            <span class="badge">2</span>平台选择<span class="chevron"></span>
                        </li>
                        <li data-target="#step-environment" class="">
                            <span class="badge">3</span>环境检测<span class="chevron"></span>
                        </li>
                        <li data-target="#step-database" class="">
                            <span class="badge">4</span>数据库<span class="chevron"></span>
                        </li>
                        <li data-target="#step-account" class="">
                            <span class="badge">5</span>初始帐号<span class="chevron"></span>
                        </li>
                        <li data-target="#step-complete" class="">
                            <span class="badge">6</span>完成<span class="chevron"></span>
                        </li>
                    </ul>
                </div>
                <div class="step-content">
                    <div class="step-pane active" id="step-license"></div>
                    <div class="step-pane" id="step-platform"></div>
                    <div class="step-pane" id="step-environment"></div>
                    <div class="step-pane" id="step-database"></div>
                    <div class="step-pane" id="step-account"></div>
                    <div class="step-pane" id="step-complete"></div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p align="center">
            <code><?php echo CMS_VERSION; ?></code>
            Installer ©<?php echo date('Y'); ?>
        </p>
    </footer>
</div>
<script src="static/js/require.js" data-main="static/js/main"></script>
</body>
</html>
