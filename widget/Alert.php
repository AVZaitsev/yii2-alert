<?php

namespace avzaitsau\alert\widget;

use Yii;
use yii\bootstrap5\Html;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 */
class Alert extends \yii\bootstrap5\Widget
{
    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - key: the name of the session flash variable
     * - value: the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error'   => 'bg-danger',
        'danger'  => 'bg-danger',
        'success' => 'bg-success',
        'info'    => 'bg-info',
        'warning' => 'bg-warning'
    ];

    protected $alertTitles = [];
    /**
     * @var array the options for rendering the close button tag.
     * Array will be passed to [[\yii\bootstrap\Alert::closeButton]].
     */
    public $closeButton = [];


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->alertTitles = [
            'error'   => Yii::t('app-backend', 'Памылка'),
            'danger'  => Yii::t('app-backend', 'Увага!'),
            'success' => Yii::t('app-backend', 'Паспяхова'),
            'info'    => Yii::t('app-backend', 'Інфармацыя'),
            'warning' => Yii::t('app-backend', 'Папярэджанне')
        ];

        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $appendClass = isset($this->options['class']) ? ' ' . $this->options['class'] : '';

        echo '<div aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 w-100">';
        echo Html::beginTag('div', ['class' => 'toast-container position-absolute bottom-0 end-0 p-3', 'style' => 'z-index: 11;', 'id' => "avzaitsau-alert"]);
        foreach ($flashes as $type => $flash) {
            if (!isset($this->alertTypes[$type])) {
                continue;
            }

            foreach ((array) $flash as $i => $message) {
                echo \yii\bootstrap5\Toast::widget([
                    'title' => '<div class="rounded ' . $this->alertTypes[$type] . '" style="width: 20px; height: 20px;"></div>' . $this->alertTitles[$type],
                    'titleOptions' => ['class' => 'd-flex gap-2 '],
                    'body' => $message,
                    'closeButton' => $this->closeButton,
                    'options' => array_merge($this->options, [
                        'class' => 'toast fade show' . $appendClass,
                    ]),
                ]);
            }

            $session->removeFlash($type);
        }
        echo Html::endTag('div');
        echo Html::endTag('div');
    }
}
