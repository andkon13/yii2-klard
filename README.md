# yii2-klard
widgets for easy integration kladr.ru in Yii2

example:
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

