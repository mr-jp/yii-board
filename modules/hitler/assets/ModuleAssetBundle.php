<?php
namespace app\modules\hitler\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ModuleAssetBundle extends AssetBundle
{
    public $sourcePath = '@app/modules/hitler/web';
    public $css = [
        'css/hitler.css',
    ];
}