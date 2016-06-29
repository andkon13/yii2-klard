<?php

namespace andkon\yii2kladr\assets;

use yii\web\AssetBundle;

/**
 * Class KladrAsset
 *
 * @package common\components\kladr\assets
 */
class KladrAsset extends AssetBundle
{
    public $sourcePath = '@common/components/yii2kladr/assets/kladrapi-jsclient/';
    public $css = [
        'jquery.kladr.min.css'
    ];
    public $js = [
        'jquery.kladr.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
