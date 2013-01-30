<?php

Yii::import('bootstrap.helpers.TbHtml');

/**
 * Bootstrap API component.
 */
class TbApi extends CApplicationComponent
{
    /**
     * @var bool whether we should copy the asset file or directory even if it is already published before.
     */
    public $forceCopyAssets = false;

    private $_assetsUrl;

    /**
     * Registers the Bootstrap CSS.
     */
    public function registerCoreCss()
    {
        $filename = YII_DEBUG ? 'bootstrap.css' : 'bootstrap.min.css';
        Yii::app()->clientScript->registerCssFile($this->getAssetsUrl() . '/css/' . $filename);
    }

    /**
     * Registers the responsive Bootstrap CSS.
     */
    public function registerResponsiveCss()
    {
        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();
        $cs->registerMetaTag('width=device-width, initial-scale=1.0', 'viewport');
        $filename = YII_DEBUG ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css';
        $cs->registerCssFile($this->getAssetsUrl() . '/css/' . $filename);
    }

    /**
     * Registers all Bootstrap CSS files.
     */
    public function registerAllCss()
    {
        $this->registerCoreCss();
        $this->registerResponsiveCss();
    }

    /**
     * Registers jQuery and Bootstrap JavaScript.
     * @param int $position the position of the JavaScript code.
     */
    public function registerCoreScripts($position = CClientScript::POS_END)
    {
        // todo: register tooltip & popover
        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        $filename = YII_DEBUG ? 'bootstrap.js' : 'bootstrap.min.js';
        $cs->registerScriptFile($this->getAssetsUrl() . '/js/' . $filename, $position);
    }

    /**
     * Registers the Tooltip and Popover plugins.
     */
    public function registerTooltipAndPopover()
    {
        $this->registerPopover();
        $this->registerTooltip();
    }

    /**
     * Registers all Bootstrap JavaScript files.
     */
    public function registerAllScripts()
    {
        $this->registerCoreScripts();
        $this->registerTooltipAndPopover();
    }

    /**
     * Registers all assets.
     */
    public function register()
    {
        $this->registerAllCss();
        $this->registerAllScripts();
    }

    /**
     * Registers the Bootstrap Popover plugin.
     * @param string $selector the CSS selector.
     * @param array $options plugin JavaScript options.
     * @see http://twitter.github.com/bootstrap/javascript.html#popover
     */
    public function registerPopover($selector = 'body', $options = array())
    {
        if (!isset($options['selector']))
            $options['selector'] = 'a[rel=popover]';
        $this->registerPlugin(TbHtml::PLUGIN_POPOVER, $selector, $options);
    }

    /**
     * Registers the Bootstrap Tooltip plugin.
     * @param string $selector the CSS selector.
     * @param array $options plugin JavaScript options.
     * @see http://twitter.github.com/bootstrap/javascript.html#tooltip
     */
    public function registerTooltip($selector = 'body', $options = array())
    {
        if (!isset($options['selector']))
            $options['selector'] = 'a[rel=tooltip]';
        $this->registerPlugin(TbHtml::PLUGIN_TOOLTIP, $selector, $options);
    }

    /**
     * Registers a specific Bootstrap plugin with the given selector and options.
     * @param string $name the plugin name.
     * @param string $selector the CSS selector.
     * @param array $options plugin JavaScript options.
     * @param int $position the position of the JavaScript code.
     */
    public function registerPlugin($name, $selector, $options = array(), $position = CClientScript::POS_END)
    {
        // Generate a "somewhat" unique id for the script snippet.
        $id = __CLASS__ . '#' . sha1($name . $selector . serialize($options) . $position);
        $options = !empty($options) ? CJavaScript::encode($options) : '';
        Yii::app()->clientScript->registerScript($id, "jQuery('{$selector}').{$name}({$options});", $position);
    }

    /**
     * Returns the url to the published assets folder.
     * @return string the url.
     */
    protected function getAssetsUrl()
    {
        if (isset($this->_assetsUrl))
            return $this->_assetsUrl;
        else
        {
            $assetsPath = Yii::getPathOfAlias('bootstrap.assets');
            $assetsUrl = Yii::app()->assetManager->publish($assetsPath, true, -1, $this->forceCopyAssets);
            return $this->_assetsUrl = $assetsUrl;
        }
    }
}