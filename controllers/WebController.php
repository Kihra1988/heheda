<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/30
 * Time: 17:20
 */

namespace app\controllers;

use app\models\UploadForm;
use app\models\UserPic;
use yii\web\Controller;
use yii\data\Pagination;
use Yii;

class WebController extends Controller
{

    public function actionIndex()
    {
        $query =  UserPic::find()->where('status > 0');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $pagination->defaultPageSize = 4;
        $picInfo = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        $fileModel = new UploadForm();
        return $this->renderPartial('/web/index',['picInfo'=>$picInfo,'pagination'=>$pagination,'model'=>$fileModel]);
    }
    public function actionUpload(){
            var_dump($_FILES);die;

    }


}