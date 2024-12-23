<?php

namespace avzaitsau\alert\controllers;

use avzaitsau\alert\widget\Alert;
use Yii;
use yii\helpers\Json;
use yii\web\Application;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * SSE server
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        ini_set('output_buffering', 0);
        ob_end_clean();
        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header('X-Accel-Charset: utf-8');
        header('X-Accel-Expires: 0');
        header('X-Accel-Redirect: no');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        Yii::$app->response->isSent = true;

        ob_implicit_flush();

        while (true) {
            if (connection_aborted()) {
                break;
            }
            $flashes = Yii::$app->session->getAllFlashes();
            if ($flashes) {
                echo ("data: " . Json::encode(['html' => Alert::widget()]) . "\n\n");
            }
            sleep(1);
        }

        Yii::$app->state = Application::STATE_END;
        Yii::$app->end();
    }
}
