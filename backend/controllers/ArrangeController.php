<?php
/**
 * file: ArrangeController.php
 * desc: 管理员日程安排 执行操作控制器
 * user: liujx
 * date: 2016-09-19 14:39:17
 */

// 引入命名空间
namespace backend\controllers;

use Yii;
use backend\models\Arrange;

class ArrangeController extends Controller
{
    /**
     * where() 查询参数配置
     * @param array $params
     * @return array
     */
    public function where($params)
    {
        return [
            'id'       => '=',
			'title'    => 'like',
			'status'   => '=',
            'admin_id' => '='
        ];
    }

    /**
     * actionIndex() 首页显示
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'status'       => Arrange::getStatus(),         // 状态
            'timeStatus'   => Arrange::getTimeStatus(),     // 时间状态
            'statusColors' => Arrange::getStatusColors(),   // 状态颜色
            'timeColors'   => Arrange::getTimeColors(),     // 时间状态颜色
        ]);
    }

    /**
     * actionCalendar()管理日程
     * @return string
     */
    public function actionCalendar()
    {
        // 查询没有委派的信息
        $arrArrange    = Arrange::find()->where(['and', ['!=', 'status', Arrange::STATUS_DEFER], ['=', 'admin_id', 0]])->orderBy(['time_status' => SORT_DESC])->all();
        // 载入视图
        return $this->render('calendar', [
            'status'       => Arrange::getStatus(),         // 状态
            'timeStatus'   => Arrange::getTimeStatus(),     // 时间状态
            'arrange'      => $arrArrange,                  // 没有委派的事件
            'statusColors' => Arrange::getStatusColors(),   // 状态颜色
            'timeColors'   => Arrange::getTimeColors(),     // 时间状态颜色
        ]);
    }

    /**
     * actionArrange 查询管理员日程信息
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionArrange()
    {
        $request = Yii::$app->request;
        $arrUserArrange = [];
        if ($request->isAjax) {
            // 查询条件
            $where = ['and', ['=', 'admin_id', Yii::$app->user->id]];
            $strStart = $request->get('start');
            $strEnd   = $request->get('end');
            if ($strStart) $where[] = ['>=', 'created_at', strtotime($strStart)];
            if ($strEnd)   $where[] = ['<',  'created_at', strtotime($strEnd)];
            // 查询管理员的日程
            $arrUserArrange = Arrange::find()->where($where)->all();
            if ($arrUserArrange) {
                $arrTmp = [];
                foreach ($arrUserArrange as $value) {
                    $arrTmp[] = [
                        'id'          => $value->id,
                        'title'       => $value->title,
                        'start'       => date('Y-m-d H:i:s', $value->start_at),
                        'desc'        => $value->desc,
                        'status'      => $value->status,
                        'end'         => date('Y-m-d H:i:s', $value->end_at),
                        'time_status' => $value->time_status,
                        'className'   => Arrange::getStatusColors($value->status),
                    ];
                }

                $arrUserArrange = $arrTmp;
            }
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;   // json 返回
        return $arrUserArrange;
    }

    /**
     * getModel() 获取model
     * @return Arrange
     */
    public function getModel()
    {
        return new Arrange();
    }

    /**
     * handleExport() 导出数据显示问题(时间问题可以通过Excel自动装换)
     */
    public function handleExport(&$objModel)
    {
        $objModel->start_at   = date('Y-m-d H:i:s', $objModel->start_at);
        $objModel->end_at     = date('Y-m-d H:i:s', $objModel->end_at);
        $objModel->created_at = date('Y-m-d H:i:s', $objModel->created_at);
        $objModel->updated_at = date('Y-m-d H:i:s', $objModel->updated_at);
    }
}
