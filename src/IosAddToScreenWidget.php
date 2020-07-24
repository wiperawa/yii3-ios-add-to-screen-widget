<?php

declare(strict_types=1);

namespace wiperawa\pwa\IosAddToScreen;

use App\ApplicationParameters;
use phpDocumentor\Reflection\Types\Self_;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetManager;
use Yiisoft\View\View;
use Yiisoft\View\WebView;
use Yiisoft\Widget\Widget;

final class IosAddToScreenWidget extends Widget
{

    /**
     * @var array widget container options
     */
    private array $containerOptions = [];

    /**
     * @var array default container options
     */
    private static array $defaultContainerOptions = ['class' => 'pwa-ios-install-container'];

    /**
     * @var string app brand img url
     */
    private string $brandImg = "@iosWidgetAssetUrl/img/brand-yii.png";

    /**
     * @var string welcome widget text
     */
    private string $welcomeText = "Install @appName on your IPhone: ";

    /**
     * @var string instruction text
     */
    private string $instructionText = "push @iosShareImg and then @iosAddImg to screen 'Hone'";

    /**
     * @var string
     */
    private $iosShareImg = "@iosWidgetAssetUrl/img/ios-share.svg";

    /**
     * @var string
     */
    private $iosAddImg = "@iosWidgetAssetUrl/img/ios-add.svg";

    /**
     * @var int cookie life time in seconds
     */
    private int $cookieLifeTime = 365 * 24 * 60 * 60;

    /**
     * @var string cookie name
     */
    private string $cookieName = 'ios_add_app_dialog_closed';

    /**
     * @var string view file path
     */
    private string $viewPath = "@iosWidgetBasePath/view/ios.php";


    private WebView $view;

    private Aliases $aliases;

    private AssetManager $assetManager;

    private ApplicationParameters $applicationParameters;

    /**
     * IosAddToScreenWidget constructor.
     * @param WebView $view
     * @param ApplicationParameters $applicationParameters
     */
    public function __construct(
        WebView $view,
        Aliases $aliases,
        AssetManager $assetManager,
        ApplicationParameters $applicationParameters

    )
    {
        $this->view = $view;
        $this->aliases = $aliases;
        $this->assetManager = $assetManager;
        $this->applicationParameters = $applicationParameters;
    }


    /**
     * Main entry point of widget
     * @return string HTML of rendered widget
     */
    public function run(): string
    {
        if ($_COOKIE[$this->cookieName] ?? false) {
            return '';
        }

        $aliases = $this->aliases;

        $this->registerAssets();

        $this->registerJs();

        return $this->view->renderFile($aliases->get($this->viewPath), [
            'containerOptions' => $this->containerOptions,
            'appName' => $this->applicationParameters->getName(),
            'brandImg' => $aliases->get($this->brandImg),
            'welcomeText' => $this->welcomeText,
            'instructionText' => $this->instructionText,
            'iosShareImg' => $this->iosShareImg,
            'iosAddImg' => $this->iosAddImg,
            'aliases' => $aliases
        ]);

    }

    public function withContainerOptions(array $options) :self
    {
        $new = clone $this;
        $new->containerOptions = array_merge($options, self::$defaultContainerOptions);
        return $new;
    }

    public function withBrandImg(string $imgSrc)
    {
        $new = clone $this;
        $new->brandImg = $imgSrc;
        return $new;
    }

    public function withWelcomeTest(string $text)
    {
        $new = clone $this;
        $new->welcomeText = $text;
        return $new;
    }

    public function withInstructionText( string $text)
    {
        $new = clone $this;
        $new->instructionText = $text;
        return $new;
    }

    public function withIosShareImg(string $imgSrc)
    {
        $new = clone $this;
        $new->iosShareImg = $imgSrc;
        return $new;
    }

    public function withIosAddImg(string $imgSrc)
    {
        $new = clone $this;
        $new->iosAddImg = $imgSrc;
        return $new;
    }

    public function withView(string $viewPath)
    {
        $new = clone $this;
        $new->viewPath = $viewPath;
        return $new;
    }

    private function registerAssets()
    {

        $assetManager = $this->assetManager;
        $aliases = $this->aliases;

        $aliases->set('@iosWidgetBasePath', dirname(__FILE__));

        $assetManager->register([IosAddToScreenWidgetAsset::class]);
        $assetPath = $assetManager->getBundle(IosAddToScreenWidgetAsset::class)->baseUrl;

        $aliases->set('@iosWidgetAssetUrl', $assetPath);

        $this->welcomeText = str_ireplace(
            '@appName',
            $this->applicationParameters->getName(),
            $this->welcomeText
        );

    }

    private function registerJs()
    {
        $js = <<<JS
            const isIos = () => {
                const userAgent = window.navigator.userAgent.toLowerCase();
                return /iphone|ipad|ipod/.test( userAgent );
            };
            const isInStand = () => ('standalone' in window.navigator) && (window.navigator.standalone);
            if (isIos() && !isInStand()) {
                document.querySelector('.pwa-ios-install-container').style.display = 'block';
            }

            document.querySelector('.pwa-ios-close-install').addEventListener('click', function(e)
            {
                let exDate = new Date();
                exDate.setSeconds(exDate.getSeconds() + {$this->cookieLifeTime});
                let cValue = '1' + "; sameSite=Lax; expires=" + exDate.toUTCString();
                document.cookie = "{$this->cookieName}=" + cValue;
                document.querySelector('.pwa-ios-install-container').style.display = 'none';
            });

        JS;

        $this->view->registerJs($js, WebView::POSITION_END);
    }
}
