<?php
/**
 * AbstractModel class file.
 * @copyright (c) 2015, Pavel Bariev
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\abstractModule\models;

use Yii;
use \yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;

/**
 * Description.
 *
 * Usage:
 * @author Pavel Bariev <bariew@yandex.ru>
 *
 */
class AbstractModel extends ActiveRecord
{
    /**
     * Gets class name for a parent model for the current module's model.
     * @param bool $asModel
     * @return static|string
     */
    public static function parentClass($asModel = false)
    {
        $class = array_values(class_parents(get_called_class()))[2];
        return $asModel ? new $class() : $class;
    }

    /**
     * Gets class name for a model that inherits current modules model.
     * CAUTION! This works only when called from inside another module model
     * @param bool $asModel
     * @param array $initData
     * @return string|static
     */
    public static function childClass($asModel = false, $initData = [])
    {
        $data = debug_backtrace();
        $callingClassName = get_class($data[1]['object']);
        $pattern = '#^(.+\\\\)(\w+\\\\\w+)$#';
        $formName = preg_replace($pattern, '$2', get_called_class());
        $result = preg_replace($pattern, '$1'.$formName, $callingClassName);
        return $asModel ? new $result($initData) : $result;
    }

    /**
     * @param $className
     * @return mixed
     */
    public static function moduleName($className)
    {
        return preg_replace('#.*\\\\(\w+)\\\\\w+\\\\\w+$#','$1', $className);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $name = preg_replace('#.*\\\\(\w+)\\\\models\\\\(\w+)$#', '$1$2', static::className());
        return '{{%'.Inflector::camel2id($name, '_').'}}';
    }

    /**
     * Gets search query.
     * @param array $params search params key=>value
     * @return ActiveQuery
     */
    public function search($params = [])
    {
        return $this::find()->andFilterWhere(array_merge($this->attributes, $params));
    }
}
