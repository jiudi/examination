<?php

$this->title = '全真模拟';
$this->registerCssFile('@web/css/imitate.css');

?>
<?=$this->render('_crumbs')?>
<div class="info-up clearfix row">
    <div class="infoup-left clearfix pull-left col-md-3">
        <fieldset class="kaochang-info">
            <legend>理论考试</legend>
            <span>第01考台</span>
        </fieldset>
        <fieldset class="kaosheng-info">
            <legend>考生信息</legend>
            <div>
                <div class="info-img">
                    <img src="/images/diandian.png">
                </div>
                <p class="name">
                    考生姓名：<span class="nickname ellipsis">我是车神</span>
                </p>
                <p>考试题数：50题</p>
                <p>考试时间：30分钟</p>
                <p>合格标准：满分100分</p>
                <p class="text-right">90及格</p>
            </div>
        </fieldset>
    </div>
    <div class="infoup-center pull-right col-md-9">
        <fieldset class="kaochang-main">
            <legend>考试题目</legend>
            <div class="timu-container">
                <div class="timu-item clearfix">
                    <div class="timu-content">
                        <div class="pull-left">
                            <div class="timu-x">
                                <p class="timu-p">1.路中黄色斜线填充标记警告前方有固定性障碍物。</p>
                            </div>
                            <p>A、正确</p>
                            <p>B、错误</p>
                        </div>
                        <div class="pull-right">
                            <img class="clearfix" src="http://file.open.jiakaobaodian.com/tiku/res/952000.jpg" style="width:300px; height:120px;">
                            <p class="text-center"><a class="see-img">查看大图</a></p>
                        </div>
                    </div>
                    <div class="result-container pull-right row">
                        <div class="result-info col-md-6"></div>
                        <div class="options-container col-md-6 text-left">
                            <label>请选择：</label>
                            <button type="button" class="btn btn-default option-btn" data-answer="16">A</button>
                            <button type="button" class="btn btn-default option-btn" data-answer="32">B</button>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>
<div class="info-middle clearfix row">
    <div class="col-md-3">
        <fieldset class="time-info pull-left">
            <legend>剩余时间</legend>
            <span data-item="left-time">17:49</span>
        </fieldset>
    </div>
    <div class="col-md-6">
        <fieldset class="tip-container pull-left">
            <legend>提示信息</legend>
            <div class="tip-content" data-item="tip-content">判断题，请判断对错！</div>
        </fieldset>
    </div>
    <div class="fun-btns col-md-3">
        <button type="button" class="btn btn-info" id="prev">上一题</button>
        <button type="button" class="btn btn-info" id="next">下一题</button>
        <button type="button" class="btn btn-success pull-right">交卷</button>
    </div>
</div>
<div class="info-down clearfix">
    <fieldset>
        <legend>答题信息</legend>
        <div class="datika-container" data-item="datika-container">
            <ul class="datika">
                <li class="row0 col0 current" data-id="952000">1</li>
                <li class="row0 col0 dui current" data-id="952000">1</li>
                <li class="row0 col0 cuo current" data-id="952000">1</li>
                <?php for($i = 2; $i <= 100; $i ++) :?>
                <li class="row0 " data-id="946200"><?=$i?></li>
                <?php endfor; ?>
            </ul>
        </div>
    </fieldset>
</div>