<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $img
 * @property string $img_md5
 * @property string $user_address
 * @property integer $create_at
 */
class UserPic extends ActiveRecord
{
    const STATUS_DEL   = -1;
    const STATUS_CHECK = 0;
    const STATUS_OK    = 1;
    const STATUS_CHAIN = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_pic}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_OK],
            ['status', 'in', 'range' => [self::STATUS_OK, self::STATUS_DEL,self::STATUS_CHAIN,self::STATUS_CHECK]],
        ];
    }

}
