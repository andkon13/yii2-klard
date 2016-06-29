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

    static private $inputs = [];

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

    public function run()
    {
        $fakeId                    = $this->id . '_kladr';
        $fakeName                  = $this->name . '_kladr';
        self::$inputs[$this->type] = [$this->id, $fakeId];

        $options = array_merge($this->options, ['id' => $fakeId]);
        $this->registryJsForInput($fakeId, $this->id, $this->value);

        echo Html::textInput($fakeName, '', $options);
        $options = array_merge($this->options, ['id' => $this->id]);
        echo Html::hiddenInput($this->name, $this->value, $options);
    }

    private function registryJsForInput($fakeId, $id, $value = null)
    {
        switch ($this->type) {
            case self::TYPE_STREET:
                $script = '$("#' . $fakeId . '")
                .kladr({type: "' . $this->type . '", parentType: $.kladr.type.city, parentInput:"#' . self::$inputs[self::TYPE_CITY][1] . '"})';
                break;
            case self::TYPE_BUILDING:
                $script = '$("#' . $fakeId . '")
                .kladr({type: "' . $this->type . '", parentType: $.kladr.type.street, parentInput:"#' . self::$inputs[self::TYPE_STREET][1] . '"})';
                break;
            case self::TYPE_ZIP:
                $script = '$("#' . $fakeId . '").kladrZip($("body"))';
                break;
            default:
                $script = '$("#' . $fakeId . '").kladr({type: "' . $this->type . '"})';
        }

        $script .= '.change(
            function(event){
                $("#' . $id . '").val($(event.target).attr("data-kladr-id"));
            }
        )';

        $this->getView()->registerJs($script);
    }
}
