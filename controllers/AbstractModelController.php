<?php
/**
 * ItemController class file.
 * @copyright (c) 2015, Bariev Pavel
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace bariew\abstractModule\controllers;

use bariew\abstractModule\actions\CreateAction;
use bariew\abstractModule\actions\DeleteAction;
use bariew\abstractModule\actions\DeleteAllAction;
use bariew\abstractModule\actions\IndexAction;
use bariew\abstractModule\actions\UpdateAction;
use bariew\abstractModule\actions\ViewAction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use bariew\abstractModule\models\AbstractModel;

/**
 * For managing abstract items.
 *
 * @author Pavel Bariev <bariew@yandex.ru>
 */
class AbstractModelController extends Controller
{
    public $modelName = '$2';

    public $createRedirectAction = 'view';
    public $updateRedirectAction = 'view';
    public $deleteRedirectAction = ['index'];

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
            ],
            'view' => [
                'class' => ViewAction::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'redirectAction' => $this->createRedirectAction,
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'redirectAction' => $this->updateRedirectAction,
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'redirectAction' => $this->deleteRedirectAction,
            ],
            'delete-all' => [
                'class' => DeleteAllAction::className(),
                'redirectAction' => $this->deleteRedirectAction,
            ],
        ];
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer|boolean $id
     * @param boolean $search
     * @return AbstractModel the loaded model
     * @throws NotFoundHttpException
     */
    public function findModel($id = false, $search = false)
    {
        $class = static::getModelClass($this->modelName) . ($search ? 'Search' : '');
        $model = new $class();
        if ($id && (!$model = $model->search(compact('id'))->one())) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $model;
    }

    /**
     * @param $modelName
     * @param bool|array $init
     * @return mixed
     */
    protected static function getModelClass($modelName, $init = false)
    {
        $class = preg_replace(
            '#^(.+)\\\\controllers\\\\(.+)Controller#',
            '$1\\\\models\\\\'.$modelName,
            static::className()
        );
        return ($init === false) ? $class : new $class($init);
    }
}
