<?php

declare(strict_types=1);

namespace wiperawa\pwa\IosAddToScreen;

use Yiisoft\Assets\AssetBundle;


class IosAddToScreenWidgetAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public ?string $sourcePath = '@iosWidgetBasePath/assets';

    public array $css = [
        'css/ios-install-widget.css'
    ];

}
