<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\helpers\Url;
use yii\captcha\Captcha;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>考试系统 - <?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"> 考试系统 </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav pull-right">
                <li class="active"><a href="/">首页</a></li>
                <li><a href="javascript:;" class="login no-login <?=Yii::$app->user->isGuest ? '' : 'hide'?>">登录</a></li>
                <li><a href="javascript:;" class="register no-login <?=Yii::$app->user->isGuest ? '' : 'hide'?>">注册</a></li>
                <li class="dropdown user-login <?=Yii::$app->user->isGuest ? 'hide' : ''?>">
                    <a href="#" class="dropdown-toggle user" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <img class="nav-user-photo" id="user-face" src="<?=Yii::$app->user->isGuest || ! Yii::$app->user->identity->face ? '/images/avatar.jpg' : Yii::$app->user->identity->face?>" alt="<?=Yii::$app->user->isGuest ? '' : (Yii::$app->user->identity->username ? Yii::$app->user->identity->username : Yii::$app->user->identity->email)?>">
                        <span class="user-info"><small>Welcome,</small> <span class="text-danger" id="username"><?=Yii::$app->user->isGuest ? '' : (Yii::$app->user->identity->username ? Yii::$app->user->identity->username : Yii::$app->user->identity->email)?></span></span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-header"> 我的信息 </li>
                        <li><a href="/resource/ace"> 我的错题 </a></li>
                        <li><a href="/resource/Simpli"> 我的收藏 </a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">其他操作</li>
                        <li><a href="<?=Url::toRoute(['site/logout'])?>" class=""> 退出登录 </a></li>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<div class="container theme-showcase main" id="content" role="main">
    <div class="row">
        <div class="col-md-12">
            <?= $content ?>
        </div>
    </div>
</div> <!-- /container -->

<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="jkbd-width wid-auto">
                    <ul class="cl">
                        <li><a rel="nofollow"  href="/about/intro.html" class="joinus a-link">关于我们</a></li>
                        <li class="bd-line"></li>
                        <li class="fankui" data-item="feedback"><a href="/feedback" class="a-link">意见反馈</a></li>
                    </ul>
                    <p>Copyright © 2012 北京木仓科技有限公司版权所有&nbsp;&nbsp;京ICP备11009001号-17</p>
                    <img class="a-wap a-dis icon" src="http://web.resource.mucang.cn/jiakaobaodian.web/jkbd/resources/images/public/gongan.png">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="login-dialog hide member-dialog" id="login-dialog">
    <div>
        <p class="tips normal"> 注册/登录后可保存做题进度 </p>
    </div>
    <div class="content-container">
        <form class="login-form form-horizontal user-form" id="login-form" action="<?=Url::toRoute(['site/login'])?>">
            <input type="hidden" value="<?php echo Yii::$app->getRequest()->getCsrfToken(); ?>" name="_csrf" />
            <div class="form-group">
                <input name="username" class="i-username form-control" required="true" email="true" rangelength="[2, 100]" placeholder="请输入账号邮箱" type="text" />
            </div>
            <div class="form-group">
                <input name="password" class="i-password form-control" required="true" rangelength="[6, 50]" placeholder="请输入您的密码" type="password" />
            </div>
            <div class="form-group">
                <button type="submit" class="submit btn btn-info">登 录</button>
            </div>
            <div class="form-group">
                <span class="show-span pull-left"><a href="javascript:;" class="register">立即注册</a></span>
            </div>
        </form>
    </div>
</div>

<div class="register-dialog member-dialog hide" id="register-dialog">
    <div>
        <p class="tips normal"> 注册/登录后可保存做题进度 </p>
    </div>
    <div class="content-container">
        <form class="register-form form-horizontal user-form" id="register-form" action="<?=Url::toRoute(['site/register'])?>">
            <input type="hidden" value="<?php echo Yii::$app->getRequest()->getCsrfToken(); ?>" name="_csrf" />
            <div class="form-group">
                <input name="username" class="form-control" required="true" rangelength="[2, 100]" placeholder="请输入昵称" type="text">
            </div>
            <div class="form-group">
                <input name="email" class="i-username form-control" required="true" email="true" rangelength="[2, 100]" placeholder="请输入登录邮箱" type="text">
            </div>
            <div class="form-group">
                <input name="password" id="m-password" class="i-password form-control" required="true" rangelength="[6, 50]"  placeholder="请设置密码" type="password">
            </div>
            <div class="form-group">
                <input name="rePassword" class="form-control" required="true" rangelength="[6, 50]" equalTo="#m-password" placeholder="确认密码" type="password">
            </div>
            <div class="form-group">
                <div class="col-sm-6 pl-none">
                    <input name="verifyCode" class="captchaCode form-control pull-left" required="true" minlength="6" maxlength="6" placeholder="请输入图片验证码" type="text">
                </div>
                <div class="col-sm-6">
                    <?=Captcha::widget([
                        'name'          => 'captchaimg',
                        'captchaAction' => 'site/captcha',
                        'imageOptions'  => [
                            'id'    => 'captchaimg',
                            'title' => '换一个',
                            'alt'   => '换一个',
                            'style' => 'cursor:pointer;margin-left:25px;'
                        ],
                        'template' => '{image}'
                    ])?>
                </div>
            </div>
            <div class="form-group">
                <p class="other-tips">点击“注册”按钮，既表示你同意<a rel="nofollow" target="_blank" href="http://www.jiakaobaodian.com/member/protocol.html">《用户协议》</a></p>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-info submit">立即注册</button>
            </div>
            <div class="form-group">
                <span class="show-span pull-left"><a href="javascript:;" class="login">有账号，去登录</a></span>
            </div>
        </form>
    </div>
</div>

<?php $this->endBody() ?>
<script type="text/javascript">
    // 设置高度
    var iWHeight = $(window).height() - 187, iCHeight = $('#content').height();
    if (iWHeight > iCHeight) $('#content').css('min-height', iWHeight + 'px');

    var loginShow, registerShow, oLoading;

    // 弹出窗口
    function showDialog(selector, sTitle, params) {
        if ( ! params) params = {};
        params = $.extend({
            modal:      true,   // 是否模块化
            title:      sTitle, // 标题
            width:      400,    // 宽度
            resizable:  false   // 是否允许改变大小
        }, params);

        $(selector).find('label.error').remove();
        return $(selector).removeClass('hide').dialog(params);
    }

    // 弹出登录窗口
    function showLogin()
    {
        if (registerShow) registerShow.dialog('close');
        loginShow = showDialog('#login-dialog', '考试系统登录');
    }

    // 弹出注册窗口
    function showRegister()
    {
        if (loginShow) loginShow.dialog('close');
        registerShow = showDialog('#register-dialog', '考试系统注册');
    }

    // 用户登录操作
    function userLogin(user) {
        // 关闭弹窗
        if (loginShow) loginShow.dialog('close');
        if (registerShow) registerShow.dialog('close');

        // 隐藏没有登录显示登录信息
        $('.no-login').hide();
        $('.user-login').removeClass('hide').show();
        $('#username').html(user.username ? user.username : (user.email ? user.emial : ''));
        if(user.face) $('#user-face').src(user.face);
    }

    // 弹出登录窗口
    $('.login').click(function(){
        showLogin();
    });

    // 弹出注册窗口
    $('.register').click(function(){
        showRegister();
    });

    // 用户登录
    $('.user-form').submit(function(e){
        e.preventDefault();
        // 验证数据
        if ($(this).validate().form()) {
            oLoading = layer.load();
            // ajax请求
            $.ajax({
                url:      $(this).attr('action'),
                type:     'POST',
                data:     $(this).serialize(),
                dateType: 'json'
            }).always(function(){
                layer.close(oLoading);
            }).done(function(json){
                if (json.errCode == 0) {
                    layer.msg(json.errMsg, {icon:6});
                    userLogin(json.data);
                } else {
                    if ($('.user-form').hasClass('register-form')) {
                        $('#captchaimg').trigger('click');
                    }
                    layer.msg(json.errMsg, {icon:5});
                }
            }).fail(function(error){
                console.info(error);
                layer.msg('服务器繁忙请稍候再试...');
            });
        }

        return false;
    })
</script>
<?=$this->blocks['javascript']?>
</body>
</html>
<?php $this->endPage() ?>
