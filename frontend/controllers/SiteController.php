<?php
namespace frontend\controllers;

use common\helpers\Helper;
use common\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\RegisterForm;

/**
 * Site controller
 */
class SiteController extends \common\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 用户重复登陆设置返回信息
     * @return bool
     */
    protected function loginRepeat()
    {
        $this->arrJson = [
            'errCode' => 0,
            'errMsg'  => Yii::t('app', 'loginRepeat'),
            'data'    => [
                'username' => Yii::$app->user->identity->username,
                'email'    => Yii::$app->user->identity->email,
                'face'     => Yii::$app->user->identity->face,
            ],
        ];

        return true;
    }

    /**
     * login() 用户登录和注册成功返回
     * @param string $message
     * @return bool
     */
    protected function login($message = 'loginSuccess')
    {
        $this->arrJson = [
            'errCode' => 0,
            'errMsg'  => Yii::t('app', $message),
            'data'    => [
                'username' => Yii::$app->user->identity->username,
                'email'    => Yii::$app->user->identity->email,
                'face'     => Yii::$app->user->identity->face,
            ],
        ];

        return true;
    }

    /**
     * actionLogin() 用户登录
     * @return mixed|string
     */
    public function actionLogin()
    {
        // 用户没有登录
        if (Yii::$app->user->isGuest) {
            $model = new LoginForm();
            if ($model->load(['params' => Yii::$app->request->post()], 'params') && $model->login()) {
                $this->login();
            } else {
                $this->arrJson['errCode'] = 1;
                $this->arrJson['errMsg']  = $model->getErrorString();
            }
        } else {
            $this->loginRepeat();
        }

        return $this->returnJson();
    }

    /**
     * actionLogout用户退出
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        // 退出之前修改登录信息
        $user = User::findOne(Yii::$app->user->id);
        if ($user) {
            $user->last_time = time();
            $user->last_ip   = Helper::getIpAddress();
            $user->save();
        }
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * actionRegister() 用户注册
     * @return string|\yii\web\Response
     */
    public function actionRegister()
    {
        if (Yii::$app->user->isGuest) {
            if (Yii::$app->request->isAjax) {
                $model = new RegisterForm();
                // 数据加载成功
                if ($model->load(['params' => Yii::$app->request->post()], 'params')) {
                    if ($user = $model->register()) {
                        if (Yii::$app->getUser()->login($user)) {
                            $this->login('registerSuccess');
                        }
                    } else {
                        $this->arrJson['errCode'] = 2;
                        $this->arrJson['errMsg']  = $model->getErrorString();
                    }
                }
            }
        } else {
            $this->loginRepeat();
        }

        return $this->returnJson();
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
