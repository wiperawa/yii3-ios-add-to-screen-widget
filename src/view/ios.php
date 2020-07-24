<?php

use Yiisoft\Html\Html;

/**
 *
 *
 * @var $containerOptions array
 * @var $appName string
 * @var $brandImg string
 * @var $welcomeText string
 * @var $instructionText string
 * @var $iosShareImg string
 * @var $iosAddImg string
 * @var \Yiisoft\Aliases\Aliases $aliases
 */

$instructionText = str_ireplace(
    ['@iosShareImg', '@iosAddImg'],
    [
        Html::img($aliases->get($iosShareImg),
            ['style' => ['width' => '24px','height' => '24px']]),
        Html::img($aliases->get($iosAddImg),
            ['style' => ['width' => '24px','height' => '24px']]),
    ],
    $instructionText
);

?>

<div <?= Html::renderTagAttributes(array_merge(['class' => 'pwa-ios-install-container'], $containerOptions)) ?>>
    <div class="pwa-ios-install">
        <div class="pwa-ios-speech-bubble">
            <svg class="pwa-ios-close-install" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
            </svg>

            <img class="pwa-ios-brand-img" src="<?= $brandImg ?>" alt="<?=$appName?>"/>
            <div class="pwa-ios-instruction-div">
                <?= "{$welcomeText} {$instructionText}" ?>
            </div>
        </div>
    </div>
</div>
