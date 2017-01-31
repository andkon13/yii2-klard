<?php
/**
 * Created by PhpStorm.
 * User: andkon
 * Date: 28.06.16
 * Time: 18:09
 */

namespace andkon\yii2kladr;

use andkon\yii2kladr\assets\KladrAsset;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class Widget
 *
 * @package common\components\kladr
 */
class Kladr extends InputWidget
{
    /** область, регион */
    const TYPE_REGION = 'region';
    /**  район */
    const TYPE_DISTRICT = 'district';
    /**  населённый пункт */
    const TYPE_CITY = 'city';
    /** улица */
    const TYPE_STREET = 'street';
    /** строение */
    const TYPE_BUILDING = 'building';
    /** индекс */
    const TYPE_ZIP = 'zip';

    public $type;

    public $containerTag = 'span';
    public $containerOptions = [];

    protected $containerId;

    static protected $inputs = [];

    /** @inheritdoc */
    public function init()
    {
        if (!$this->type) {
            throw new \Exception('Need set type');
        }

        if (!$this->name) {
            $this->name  = Html::getInputName($this->model, $this->attribute);
            $this->id    = Html::getInputId($this->model, $this->attribute);
            $this->value = $this->model->{$this->attribute};
        }

        KladrAsset::register($this->getView());
    }

    /** @inheritdoc */
    public function run()
    {
        $this->containerId            = KladrApi::KLADR_CACHE_PREFIX . \Yii::$app->getSecurity()->generateRandomString(10);
        $this->containerOptions['id'] = $this->containerId;
        echo Html::beginTag($this->containerTag, $this->containerOptions);
        $name = explode(']', $this->name);
        if ($pos = strpos($name[0], '_id')) {
            $fakeName = substr($name[0], 0, $pos);
        } else {
            $fakeName = $name[0] . '_name';
        }
        if (isset($name[1])) {
            $fakeName .= ']';
        }

        $fakeId                    = $this->id . '_kladr';
        self::$inputs[$this->type] = [$this->id, $this->containerId . ' #' . $fakeId];

        $options = array_merge($this->options, ['id' => $fakeId, 'data-kladr-id' => $this->value]);
        $this->registryJsForInput($fakeId, $this->id);

        $value = $this->value;
        if ($this->value && !isset($this->options['value'])) {
            switch ($this->type) {
                case self::TYPE_BUILDING:
                    $obj = KladrApi::getBuilding($this->value);
                    break;
                case self::TYPE_CITY:
                    $obj = KladrApi::getCity($this->value);
                    break;
                case self::TYPE_STREET:
                    $obj = KladrApi::getStreet($this->value);
                    break;
                default:
                    $obj = [];
                    break;
            }
            if (isset($obj[0], $obj[0]['name'])) {
                $value = $obj[0]['name'];
            }
        } else {
            $value = (is_array($this->options) && array_key_exists('value', $this->options))
                ? $this->options['value'] : $value;
        }
        echo Html::textInput($fakeName, $value, $options);
        $options = array_merge($this->options, ['id' => $this->id]);
        echo Html::hiddenInput($this->name, $this->value, $options);
        echo Html::endTag($this->containerTag);
    }

    /**
     * @return KladrApi
     */
    public static function getKladrApi()
    {
        return KladrApi::getInstanse();
    }

    /** @inheritdoc */
    protected function registryJsForInput($fakeId, $id)
    {
        switch ($this->type) {
            case self::TYPE_STREET:
                $script = '$("#' . $this->containerId . ' #' . $fakeId . '")
                .kladr({type: "' . $this->type . '", parentType: $.kladr.type.city, 
                parentInput:"#' . self::$inputs[self::TYPE_CITY][1] . '"})';
                break;
            case self::TYPE_BUILDING:
                $script = '$("#' . $this->containerId . ' #' . $fakeId . '")
                .kladr({type: "' . $this->type . '", parentType: $.kladr.type.street, 
                parentInput:"#' . self::$inputs[self::TYPE_STREET][1] . '"})';
                break;
            case self::TYPE_ZIP:
                $zipJs = '$("#' . self::$inputs[self::TYPE_BUILDING][1] . '")
                .kladr("select", function(obj){
                    if(obj.zip){
                        $("#' . self::$inputs[self::TYPE_ZIP][0] . '").val(obj.zip);
                        $("#' . self::$inputs[self::TYPE_ZIP][1] . '").val(obj.zip);
                    }
                });';
                $this->getView()->registerJs($zipJs);

                $script = '$("#' . $this->containerId . ' #' . $fakeId . '")';
                break;
            default:
                $script = '$("#' . $this->containerId . ' #' . $fakeId . '").kladr({type: "' . $this->type . '"})';
        }

        $script .= '.change(
            function(event){
                $("#' . $this->containerId . ' #' . $id . '").val($(event.target).attr("data-kladr-id"));
            }
        )';

        $this->getView()->registerJs($script);
    }
}
