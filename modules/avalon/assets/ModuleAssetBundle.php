<?php
namespace app\modules\avalon\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ModuleAssetBundle extends AssetBundle
{
    public $sourcePath = '@app/modules/avalon/web';
    public $css = [
        'css/avalon.css',
    ];
}