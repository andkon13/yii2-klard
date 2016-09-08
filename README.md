# yii2-klard
widgets for easy integration kladr.ru (autocomplete regions, citys, street, etc.) in Yii2

install:
composer require andkon/yii2kladr

use:
$address = new Address(); // your address model

$form = ActiveForm::begin();

echo $form->field($address, 'city_id')

    ->widget(Kladr::className(), [
            'type'    => Kladr::TYPE_CITY,
        'options' => [
            'placeHolder' => $model->getAttributeLabel('city_id'),
            'class' => 'form__input'
        ]
    ])
    ->label(false);
?>

