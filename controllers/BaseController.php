<?php

namespace frontend\controllers\web;

use common\models\MerchantProduct;
use common\models\user\UserInfo;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Base controller
 */
class BaseController extends Controller
{
    const LOGING_DEVICE_PREFIX = 'device_';
    const XJX_MERCHANT = 'cjxjx';
    const JXL_TOKEN_PREFIX = "jxl_token_";
    public $enableCsrfValidation = false;
    public $sessionId = NULL;
    public $mobile = NULl;
    public $deviceId = NULl;
    public $merchantName = NULL;
    public $merchantId = NULl;
    public $userId = NULl;
    const SUCC_CODE = '00';
    const SUCC_INFO = '成功';

    public function init()
    {
        parent::init();

        $session = Yii::$app->session;
        if ($session->isActive) {
            $sessionId = $session->getId();
        } else {
            $session->open();
            $sessionId = $session->getId();
        }
        if (Yii::$app->request->getMethod() == 'GET') {
            $this->sessionId = Yii::$app->request->get('sessionid') ? Yii::$app->request->get('sessionid') : session_id();
            $this->mobile = Yii::$app->request->get('mobilePhone');
            $this->deviceId = Yii::$app->request->get('deviceId');
            $this->merchantName = Yii::$app->request->get('merchantName') ? Yii::$app->request->get('merchantName') : self::XJX_MERCHANT;

        } else if (Yii::$app->request->getMethod() == 'POST') {
            $this->sessionId = Yii::$app->request->post('sessionid') ? Yii::$app->request->post('sessionid') : session_id();
            $this->mobile = Yii::$app->request->post('mobilePhone');
            $this->deviceId = Yii::$app->request->post('deviceId');
            $this->merchantName = Yii::$app->request->post('merchantName') ? Yii::$app->request->post('merchantName') : self::XJX_MERCHANT;
        }
        if (!empty($this->mobile)) {
            $user = UserInfo::findOne(['user_name' => $this->mobile]);
            if ($user) {
                $this->userId = $user->id;
            }
        }
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        $time = '';
        if(defined('BEGIN_TIME')) {
            $time = microtime(true) - BEGIN_TIME;
        }
        Yii::info('REQUEST ' . REQUEST_ID . ' end, url:' . Url::current() .
            '; result:' . json_encode($result, JSON_UNESCAPED_UNICODE).'; time:'.$time, 'appInfo');
        if (Yii::$app->response->format == Response::FORMAT_JSONP) {
            // jsonp返回数据特殊处理
            $callback = Html::encode(Yii::$app->request->get('callback'));
            $result = [
                'data' => $result,
                'callback' => $callback,
            ];
        }

        return $result;
    }

    /**
     * 获取APP的配置参数
     *
     * @param $appName
     * @param $param
     */
    public function getAppConfig($appName, $param)
    {
        if (empty($appName)) {
            $appName = 'jsxjx';
        }
        return $param . '_' . $appName;
    }
    /*
     * 未实现
     *
     * @param string $deviceId 设备号
     * @param string $mobile 手机号
     * @param string $merchantName 商户号
     *
     *
     * @return null|string
     */
    public function loginFrontUserByDeiceId($deviceId, $mobile, $merchantName = self::XJX_MERCHANT)
    {
        if (empty($deviceId)) {
            return null;
        }
        if (empty($mobile)) {
            $key = self::LOGING_DEVICE_PREFIX . $deviceId . $merchantName;
        } else {
            $key = self::LOGING_DEVICE_PREFIX . $deviceId . $mobile . $merchantName;
        }
        $userInfo = Yii::$app->redis->get($key);
        if (empty($userInfo)) {
            return null;
        }
        $user = unserialize($userInfo);
        return $user;
    }

    /**
     *
     * @param $key
     * @param $flag
     * @param int $time
     * @return mixed
     */
    public function checkForFront($key, $flag, $time = 30)
    {
        $redis = Yii::$app->redis;
        $expireKey = $key . $flag;
        $remainTime = $redis->ttl($expireKey);
        if ($remainTime) {
            $redis->set($expireKey, '1');
            $redis->expire($expireKey, $time);
        }
        return $remainTime;
    }

    /**
     *
     * @param $key
     * @param $flag
     */
    public function delCheckForFront($key, $flag)
    {
        $redis = Yii::$app->redis;
        $expireKey = $key . $flag;
        $redis->del($expireKey);
    }

    public function getAll()
    {
        $allData = [];
        $input = file_get_contents('php://input');
        if ($input) {
            $data = json_decode($input, true);
            $allData = array_merge($allData, $data);
        }
        $data = Yii::$app->request->get();
        $allData = array_merge($allData, $data);
        $data = Yii::$app->request->post();
        $allData = array_merge($allData, $data);
        return $allData;
    }
    /**
     * 返回错误信息
     */
    public function outputErr(string $code, string $message, array $data = [])
    {
        $result = [
            'code' => $code,
            'message' => $message,
        ];
        return array_merge($data, $result);
    }

    /**
     * 返回成功信息，并根据规则，返回过滤后的数据
     * rule 示例如下：
     * $rule = [
     *      'data1' => 'string',
     *      'data2' => 'int|integer',
     *      'data3' => 'bool|boolean',
     *      'dataArr' => [
     *          'childData1' => 'string',
     *          'childData2' => 'int|integer',
     *          'childData3' => 'bool|boolean',
     *      ],
     * ]
     *
     * @param array $data 需要返回的数据
     * @param array $rule 对$data的过滤规则
     * @param string $code 默认为成功返回值
     * @param string $message 默认为成功返回值
     */
    public function ouputSucc(array $data = [], array $rule = [], string $code = self::SUCC_CODE, string $message = self::SUCC_INFO)
    {
        if (!empty($rule)) {
            $data = $this->formatDataByRule($data, $rule);
        }

        $data['code'] = $code;
        $data['message'] = $message;
        return $data;
    }

    /**
     * 对数据进行规则匹配，格式化数据类型，并过滤多余的参数
     */
    public function formatDataByRule($data, $rule)
    {
        foreach ($rule as $key => $type) {
            if (is_array($type)) {
                $formatData[$key] = isset($data[$key]) ? $this->formatDataByRule($data[$key], $type) : $rule[$key];
            } else {
                switch ($type) {
                    case 'string':
                        $formatData[$key] = (string) ($data[$key] ?? '');
                        break;
                    case 'int':case 'integer':
                        $formatData[$key] = (int) ($data[$key] ?? 0);
                        break;
                    case 'bool':case 'boolean':
                        $formatData[$key] = (boolean) ($data[$key] ?? false);
                        break;
                    default:
                        $formatData[$key] = $data[$key] ?? '';
                }
            }
        }
        return $formatData;
    }
}
