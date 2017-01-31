<?php
/**
 * Created by PhpStorm.
 * User: daniil
 * Date: 30.06.16
 * Time: 12:29
 */

namespace andkon\yii2kladr;

/**
 * Класс запроса
 */
class Query
{
    /** @var  string Тип родительского объекта для ограничения области поиска (регион, район, город) */
    public $parentType;
    /** @var  int Идентификатор родительского объекта */
    public $parentId;
    /** @var  string Тип искомых объектов (регион, район, город) */
    public $contentType;
    /** @var  string Название искомого объекта (частично либо полностью) */
    public $contentName;
    /** @var  int Почтовый индекс */
    public $zip;
    /** @var  string Выполнить поиск по полной записи адреса, одной строкой */
    public $oneString;
    /** @var  string Получить объекты вместе с родителями */
    public $withParent;
    /** @var  string Ограничение количества возвращаемых объектов */
    public $limit;
    public $regionId;
    public $cityId;
    public $streetId;
    public $buildingId;

    /**
     * @return string
     */
    public function __toString()
    {
        $string = '';
        if ($this->parentType && $this->parentId) {
            $string .= $this->parentType . 'Id=' . $this->parentId;
        }
        if ($this->contentName) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'query=' . urlencode($this->contentName);
        }
        if ($this->contentType) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'contentType=' . $this->contentType;
        }
        if ($this->regionId) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'regionId=' . $this->regionId;
        }
        if ($this->cityId) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'cityId=' . $this->cityId;
        }
        if ($this->streetId) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'streetId=' . $this->streetId;
        }
        if ($this->buildingId) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'buildingId=' . $this->buildingId;
        }
        if ($this->zip) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'zip=' . $this->zip;
        }
        if ($this->oneString) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'oneString=1';
        }
        if ($this->withParent) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'withParent=1';
        }
        if ($this->limit) {
            if (!empty($string)) {
                $string .= '&';
            }
            $string .= 'limit=' . $this->limit;
        }

        return $string;
    }
}
