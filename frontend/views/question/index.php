<?php

use \common\models\Question;

$this->title = '顺序练习';
$this->registerCssFile('@web/css/question.css');
?>
<ol class="breadcrumb">
    <li><a href="/">科目一</a></li>
    <li class="active"><a class="mylink">顺序练习</a></li>
</ol>
<div class="jkbd-page-lianxi inner jkbd-width wid-auto cl lianxi-type-sequence">
    <div class="lianxi-container news-left">
        <div data-item="shiti-container" class="shiti-container " style="">
            <div class="shiti-item cl">
                <div class="clearfix">
                    <p class="shiti-content pull-left">
                        <span id="o-number">1</span>/<?=$total?>.  <?=$question->quest_title?>
                    </p>
                    <span id="user-collect" class="btn btn-default pull-right user-login favor-tag <?=Yii::$app->user->isGuest ? 'hide' : ''?>">收藏</span>
                </div>

                <?php if ($answer) : ?>
                <div class="shiti-wapper clearfix">
                    <div  class="options-container" id="answers">
                        <?php foreach ($answer as $value) : ?>
                        <p answer="<?=$value->id?>"><i></i><span><?=$value->name?></span></p>
                        <?php endforeach; ?>
<!--                        <p class="xuan"><i></i><span></span></p>-->
<!--                        <p class="dui"><i></i><span>B. 违法行为</span></p>-->
<!--                        <p class=""><i></i><span>C. 违规行为</span></p>-->
<!--                        <p class=""><i></i><span>D. 过失行为</span></p>-->
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tip-container">
            <p id="do-no-answer" class="dacuo hide">
                <label class="text-danger"> 回答错误！</label>
                正确答案：<strong id="answer-yes" class="text-success">A</strong>
            </p>
            <p class="weizuo">
                <?php
                switch ($question->answer_type) {
                    case Question::ANSWER_TYPE_ONE:
                        echo '单选题，请选择你认为正确的答案！';
                        break;
                    case Question::ANSWER_TYPE_JUDGE:
                        echo '判断题，请判断对错！';
                        break;
                    default:
                        echo '选择题，请选择你认为正确的答案！';
                }
                ?>

            </p>
        </div>
        <div class="static-container">
            错误率 <strong id="do-error-rate"><?=$question->do_number != 0 ? (round($question->error_number / $question->do_number * 100, 2 ) . '%') : '0%'?></strong> 　
            做错人数 <strong id="do-number"><?=$question->error_number?></strong> 　
            <b class="tips"> 科目一题库共 <span class="text-success"><?=$allTotal?></span> 题，已完成 <span id="do-finish" class="text-info">0</span> 题 </b>
        </div>
        <div class="shiti-buttons clearfix mt-15">
            <button id="prev" type="button" class="btn btn-info pull-left ml-15">上一题</button>
            <button id="next" type="button" class="btn btn-info pull-left ml-15">下一题</button>
<!--            <button data-item="datika" type="button" class="btn btn-default pull-right ml-15">展开答题卡</button>-->
            <button id="see-info" type="button" class="btn btn-default pull-right ml-15">查看详解</button>
        </div>
        <div class="tongji-container clearfix mt-15">
            <label class="daduinext float-l"><input type="checkbox" data-item="daduinext" checked=""><span>答对自动下一题</span></label>
            <label class="x-dadui float-l">答对：<span> <strong id="do-yes" class="text-success">0</strong> 题</span></label>
            <label class="x-dacuo float-l">答错：<span> <strong id="do-no" class="text-danger">0</strong> 题</span></label>
            <label class="x-lv float-l">正确率：<span id="do-accuracy">100%</span></label>
            <label class="x-sync float-l login no-login <?=Yii::$app->user->isGuest ? '' : 'hide'?>">登录保存做题进度</label>
        </div>

        <div id="info" class="explain-container hide">
            <div class="title">
                <span class="name">试题详解</span>
            </div>
            <div class="ef-content">
                <p class="wapper" id="question-content"><?=$question->question_content?></p>
            </div>
        </div>
    </div>
</div>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var answerYes   = '<?=$question->answer_id?>',
        iQuestionId = <?=$question->id?>,
        isCollect   = false,
        iDoFinish   = 0,
        isDo        = false;
    // 选择答案
    $('#answers p').click(function() {
        if (isDo) return false;
        isDo = true;
        // 判断对错
        if ($(this).attr('answer') == answerYes) {
            // 正确
            $(this).addClass('dui');
        } else {
            // 错误
            $(this).addClass('xuan');
            var doYes = $('#answers p[answer=' + answerYes + ']').addClass('dui');
            $('#do-no-answer').removeClass('hide').find('#answer-yes').html(doYes.text());
        }

        iDoFinish ++;
        $('#do-finish').html(iDoFinish);
    });

    // 收藏问题
    $('#user-collect').click(function(){
        if (iQuestionId > 0) {
            if (isCollect) {
                layer.msg('您之前已经收藏该问题了, 不能重复收藏哦！');
            } else {
                $.ajax({
                    url: '<?=\yii\helpers\Url::toRoute(['user/create-collect'])?>',
                    data: {
                        qid: iQuestionId
                    },
                    type: 'POST',
                    dataType: 'json'
                }).done(function(json){
                    if (json.errCode == 0) {
                        layer.msg('收藏成功', {icon:6});
                    } else {
                        layer.msg(json.errMsg, {icon:5})
                    }
                });
            }
        } else {
            layer.msg('请选择问题收藏');
        }
    });

    // 查询详情
    $('#see-info').click(function(){
        if ($("#info").hasClass('hide')) {
            $(this).html('收起详情');
            $('#info').removeClass('hide');
        } else {
            $(this).html('查看详情');
            $('#info').addClass('hide');
        }
    });
</script>
<?php $this->endBlock() ?>