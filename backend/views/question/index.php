<?php
// 定义标题和面包屑信息
$this->title = '题库信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--前面导航信息-->
<p>
    <button class="btn btn-white btn-success btn-bold me-table-insert">
        <i class="ace-icon fa fa-plus bigger-120 blue"></i>
        添加
    </button>
    <button class="btn btn-white btn-danger btn-bold me-table-delete">
        <i class="ace-icon fa fa-trash-o bigger-120 red"></i>
        删除
    </button>
    <button class="btn btn-white btn-info btn-bold me-hide">
        <i class="ace-icon fa  fa-external-link bigger-120 orange"></i>
        隐藏
    </button>
    <button class="btn btn-white btn-pink btn-bold  me-table-reload">
        <i class="ace-icon fa fa-refresh bigger-120 pink"></i>
        刷新
    </button>
    <button class="btn btn-white btn-warning btn-bold me-table-export">
        <i class="ace-icon glyphicon glyphicon-export bigger-120 orange2"></i>
        导出Excel
    </button>
</p>
<!--表格数据-->
<table class="table table-striped table-bordered table-hover" id="showTable"></table>

<div class="col-xs-12 hidden">
    <table id="detailTable" class="table table-striped table-bordered table-hover"></table>
</div>
<?php $this->beginBlock('javascript') ?>
<script type="text/javascript">
    var aSubject = <?=$subject?>,
        aSpecial = <?=$special?>,
        aChapter = <?=$chapter?>,
        aStatus  = <?=$status?>,
        aColor   = <?=$color?>,
        aType    = <?=$type?>,
        myTable = new MeTable({sTitle:"题库信息"},{
        "aoColumns":[
			oCheckBox,
			{"title": "题目ID", "data": "id", "sName": "id", "class":"details-control", "edit": {"type": "hidden", "options": {}}, "bSortable": false, "createdCell":function(td, data, rowArr, row, col){
                $(td).html(data + '<b class="arrow fa fa-angle-down pull-right"></b>');
            }},
			{"title": "题目问题", "data": "quest_title", "sName": "quest_title", "edit": {"type": "text", "options": {"required":true}}, "bSortable": false},
			{"title": "题目说明", "data": "question_content", "sName": "question_content", "edit": {"type": "text", "options": {}}, "bSortable": false}, 
			{"title": "答案类型", "data": "answer_type", "sName": "answer_type", "value": aType, "edit": {"type": "select", "options": {"required":true,"number":true}}, "bSortable": false},
			{"title": "状态", "data": "status", "sName": "status", "value": aStatus, "edit": {"type": "radio", "default": 1, "options": {"required":true,"number":true}}, "bSortable": false, "createdCell": function(td, data) {
			    $(td).html(showSpan(aStatus, aColor, data));
            }},
			{"title": "正确答案", "data": "answer_id", "sName": "answer_id", "bSortable": false, "createdCell": function(td, data) {
			    $(td).html(data == 0 ? '<span class="label label-sm label-warning">还没有设置答案</span>' : data)
            }},
			{"title": "创建时间", "data": "created_at", "sName": "created_at", "createdCell" : dateTimeString}, 
			{"title": "修改时间", "data": "updated_at", "sName": "updated_at", "createdCell" : dateTimeString}, 
			{"title": "所属科目", "data": "subject_id", "sName": "subject_id", "value": aSubject, "edit": {"type": "select", "default": 1, "options": {"required":true,"number":true}}, "bSortable": false},
			{"title": "所属章节", "data": "chapter_id", "sName": "chapter_id", "value": aChapter, "edit": {"type": "select", "options": {"required":true,"number":true}}, "bSortable": false},
			{"title": "所属专项分类", "data": "special_id", "sName": "special_id", "value": aSpecial, "edit": {"type": "select", "options": {"required":true,"number":true}}, "bSortable": false},
			{"title": "错误人数", "data": "error_number", "sName": "error_number"}, 
			oOperate
        ]

        // 设置隐藏和排序信息
        // "order":[[0, "desc"]],
        // "columnDefs":[{"targets":[2,3], "visible":false}],
    }, {
            "oTableOptions": {
                "sAjaxSource": "<?=\yii\helpers\Url::toRoute(['answer/search'])?>",
                "aoColumns":[
                    {"title": "ID", "data": "id", "sName": "id", "edit":{"type":"hidden"}},
                    {"title": "答案说明", "data": "name", "sName": "name", "edit":{"type":"text", "options":{"required": true, "rangelength": "[2, 1000]"}}},
                    oOperateDetails
                ]
            }
    });

    /**
     * 显示的前置和后置操作
     * myTable.beforeShow(object data, bool isDetail) return true 前置
     * myTable.afterShow(object data, bool isDetail)  return true 后置
     */

     /**
      * 编辑的前置和后置操作
      * myTable.beforeSave(object data) return true 前置
      * myTable.afterSave(object data)  return true 后置
      */

     myTable.init();
</script>
<?php $this->endBlock(); ?>