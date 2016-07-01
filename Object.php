<?php
/**
 * Created by PhpStorm.
 * User: daniil
 * Date: 30.06.16
 * Time: 12:31
 */

namespace andkon\yii2kladr;

/**
 * Объект КЛАДР
 */
class Object
{
    /** @var  int Идентификатор объекта */
    private $id;
    /** @var  string Название объекта */
    private $name;
    /** @var  int Почтовый индекс объекта */
    private $zip;
    /** @var  string Тип объекта полностью (область, район) */
    private $type;
    /** @var  string Тип объекта коротко (обл, р-н) */
    private $typeShort;
    /** @var  string Тип объекта из перечисления ObjectType */
    private $okato;
    /** @var  string ОКАТО объекта */
    private $contentType;
    /** @var array Массив родительских объектов */
    private $arParents;
s
    /**
     * @param $obObject
     */
    public function __construct($obObject)
    {
        $this->id          = $obObject->id;
        $this->name        = $obObject->name;
        $this->zip         = $obObject->zip;
        $this->type        = $obObject->type;
        $this->typeShort   = $obObject->typeShort;
        $this->okato       = $obObject->okato;
        $this->contentType = $obObject->contentType;
        $this->arParents   = array();
        if (isset($obObject->parents)) {
            foreach ($obObject->parents as $obParent) {
                $this->arParents[] = new Object($obParent);
            }
        }
    }
}
