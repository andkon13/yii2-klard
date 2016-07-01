<?php
/**
 * Created by PhpStorm.
 * User: daniil
 * Date: 30.06.16
 * Time: 12:32
 */

namespace andkon\yii2kladr;

/**
 * Контроллер для доступа к сервису
 *
 * @property-read string $Error Последняя ошибка
 */
class KladrApi
{
    const KLADR_CACHE_PREFIX = 'kladr_';
    private static $instance;

    private $token;
    private $key;
    private $domain;
    private $error;

    /**
     * @param string $token
     * @param string $key
     */
    public function __construct($token, $key, $domain = false)
    {
        $this->token  = $token;
        $this->key    = $key;
        $this->error  = null;
        $this->domain = 'http://kladr-api.ru/';
        if ($domain) {
            $this->domain = $domain;
        }
    }

    /**
     * @param integer $cityId
     *
     * @return array
     */
    public static function getCity($cityId)
    {
        $city = self::getFromCache(Kladr::TYPE_CITY, $cityId);
        if ($city) {
            return $city;
        }

        $query         = self::getQuery(Kladr::TYPE_CITY);
        $query->cityId = $cityId;
        $city          = self::getInstanse()->queryToArray($query);

        self::saveToCache(Kladr::TYPE_CITY, $cityId, $city);

        return $city;
    }

    /**
     * @param string  $type
     * @param integer $id
     *
     * @return mixed
     */
    private static function getFromCache($type, $id)
    {
        return \Yii::$app->getCache()->get(self::KLADR_CACHE_PREFIX . $type . '_' . $id);
    }

    /**
     * @return Query
     */
    public static function getQuery($type)
    {
        $query              = new Query();
        $query->contentType = $type;

        return $query;
    }

    /**
     * Возвращает результат запроса к сервису в виде массива
     *
     * @param Query $query Объект запроса
     *
     * @return array
     */
    public function queryToArray(Query $query)
    {
        $arr = $this->queryToJson($query, true);

        return $arr['result'];
    }

    /**
     * Возвращает результат запроса к сервису
     *
     * @param Query $query Объект запроса
     * @param bool  $assoc Вернуть ответ в виде ассоциативного массива
     *
     * @return bool|mixed
     */
    public function queryToJson(Query $query, $assoc = false)
    {
        $url = $this->getURL($query);
        if (!$url) {
            return false;
        }
        $context = stream_context_create(array('http' => array('header' => 'Connection: close\r\n')));
        $result  = file_get_contents($url, false, $context);
        if (preg_match('/Error: (.*)/', $result, $matches)) {
            $this->error = $matches[1];

            return false;
        }

        return json_decode($result, $assoc);
    }

    /**
     * @param Query $query
     *
     * @return bool|string
     */
    private function getURL(Query $query)
    {
        if (empty($this->token)) {
            $this->error = 'Токен не может быть пустым';

            return false;
        }
        if (empty($query)) {
            $this->error = 'Объект запроса не может быть пустым';

            return false;
        }

        return $this->domain . 'api.php?' . $query . '&token=' . $this->token;
    }

    /**
     * @return KladrApi
     */
    public static function getInstanse()
    {
        if (!self::$instance) {
            self::$instance = new self(\Yii::$app->params['kladrToken'], '');
        }

        return self::$instance;
    }

    /**
     * @param string  $type
     * @param integer $id
     * @param         $obj
     */
    private static function saveToCache($type, $id, $obj)
    {
        \Yii::$app->getCache()->set(self::KLADR_CACHE_PREFIX . $type . '_' . $id, $obj);
    }

    /**
     * @param integer $streetId
     *
     * @return array
     */
    public static function getStreet($streetId)
    {
        $street = self::getFromCache(Kladr::TYPE_STREET, $streetId);
        if ($street) {
            return $street;
        }
        $query           = self::getQuery(Kladr::TYPE_STREET);
        $query->streetId = $streetId;
        $street          = self::getInstanse()->queryToArray($query);

        self::saveToCache(Kladr::TYPE_STREET, $streetId, $street);

        return $street;
    }

    /**
     * @param integer $buildingId
     *
     * @return array
     */
    public static function getBuilding($buildingId)
    {
        $building = self::getFromCache(Kladr::TYPE_BUILDING, $buildingId);
        if ($building) {
            return $building;
        }
        $query             = self::getQuery(Kladr::TYPE_BUILDING);
        $query->buildingId = $buildingId;
        $building          = self::getInstanse()->queryToArray($query);

        self::saveToCache(Kladr::TYPE_BUILDING, $buildingId, $building);

        return $building;
    }

    /**
     * Возвращает результат запроса к сервису в виде массива объектов
     *
     * @param Query $query Объект запроса
     *
     * @return Object[]
     */
    public function queryToObjects(Query $query)
    {
        $obResult = $this->queryToJson($query);
        if (!$obResult) {
            return array();
        }
        if (isset($obResult->searchContext->oneString)) {
            $this->error = 'Возвращение результата в виде объектов при ' .
                'поиске по всему адресу (одной строкой) невозможен';

            return array();
        }
        $arObjects = array();
        foreach ($obResult->result as $obObject) {
            $arObjects[] = new Object($obObject);
        }

        return $arObjects;
    }

    public function __get($name)
    {
        switch ($name) {
            case 'Error':
                return $this->error;
        }
    }
}
