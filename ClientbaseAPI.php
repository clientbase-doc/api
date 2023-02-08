<?php

class ClientbaseAPI
{

    private $apiURL;
    private $token;

    public function __construct($clientbaseURL, $token)
    {
        $this->apiURL = $clientbaseURL . "/api/dev";
        $this->token = $token;
    }    
    
    
    /**
     * Получить список пользовательских таблиц
     * 
     * @return array
     */        
    public function getTablesList() 
    {
        $rawResult = $this->query("/table");
        $result = $this->_rawToResult($rawResult);

        return $result;
    }

    /**
     * Получить информацию о таблице
     * @param $tableId int id таблицы
     * @param $includeFields bool Получить информацию о полях таблицы
     * @return stdObject
     */    
    public function getTable($tableId, $includeFields = false) 
    {
        $queryParams = $includeFields ? ['include' => 'fields'] : [];
        $rawResult = $this->query("/table/" . $tableId, "GET", $queryParams);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }


    /**
     * Получить список записей таблицы
     * 
     * @param $tableId int id таблицы
     * @param $offset int Отступ от начала списка
     * @param $limit int Количество элементов
     * @param $filter mixed Фильтр в виде строки или массива 
     * @return array
     */    
    public function getDataList($tableId, $offset=0, $limit=0, $filter='') 
    {
        $queryParams = ['page' => []];
        if ($offset) {
            $queryParams['page']['offset'] = $offset;
        }
        if ($limit) {
            $queryParams['page']['limit'] = $limit;
        }
        if ($filter) {
            $queryParams['filter'] = $filter;
        }

        $rawResult = $this->query("/data" . $tableId, "GET", $queryParams);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }    

    /**
     * Получить информацию о записи в таблице
     * 
     * @param $tableId int id таблицы
     * @param $lineId int id записи в таблице
     * @return stdObject
     */
    public function getData($tableId, $lineId) 
    {
        $rawResult = $this->query("/data" . $tableId . "/" . $lineId);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }      

    /**
     * Удалить запись из таблицы
     * 
     * @param $tableId int id таблицы
     * @param $lineId int id записи в таблице
     *
     */
    public function deleteData($tableId, $lineId) 
    {
        $this->query("/data" . $tableId . "/" . $lineId, "DELETE");
    }

    /**
     * Перевести массив с данными в формат, подходящий для отправки на сервер
     * 
     * @param $data array Массив с данными для добавления/обновления записи в таблице
     * @return stdObject
     */
    public function bodyFromData($data) 
    {
        $body = new stdClass();
        $body->data = new stdClass();
        $body->data->attributes = (object) $data;

        return $body;
    }

    /**
     * Добавить строку в таблицу
     * 
     * @param $tableId int id таблицы
     * @param $data array Данные для добавления 
     * @return stdObject
     */
    public function addData($tableId, $data)
    {
        $body = $this->bodyFromData($data);
        $body->data->type = "data" . $tableId;   
        $rawResult = $this->query("/data" . $tableId . "/" . $lineId, "POST", "", $body);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }

    /**
     * Обновить строку в таблице
     * 
     * @param $tableId int id таблицы
     * @param $lineId int id записи в таблице
     * @param $data array Данные для обновления 
     * @return stdObject
     */
    public function updateData($tableId, $lineId, $data) 
    {
        $body = $this->bodyFromData($data);
        $body->data->type = "data" . $tableId;
        $body->data->id = $lineId;        
        $rawResult = $this->query("/data" . $tableId . "/" . $lineId, "PATCH", "", $body);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }

    /**
     * Получить список пользователей
     * 
     * @return array
     */
    public function getUsersList() 
    {
        $rawResult = $this->query("/user");
        $result = $this->_rawToResult($rawResult);

        return $result;
    }

    /**
     * Получить информацию о пользователе
     * 
     * @param $userId id пользователя
     * @return stdObject
     */
    public function getUser($userId) 
    {
        $rawResult = $this->query("/user/" . $userId);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }    

    /**
     * Получить список групп пользователей
     * 
     * @return array
     */
    public function getGroupsList() 
    {
        $rawResult = $this->query("/group");
        $result = $this->_rawToResult($rawResult);

        return $result;
    }      

    /**
     * Получить информацию о группе пользователей
     * 
     * @param $groupId id группы пользователей
     * @return stdObject     
     */
    public function getGroup($groupId) 
    {
        $rawResult = $this->query("/group/" . $groupId);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }      

    /**
     * Получить информацию о файле
     * 
     * @param $tableId id таблицы
     * @param $fieldId id поля таблицы
     * @param $lineId id записи таблицы
     * @param $fileName string Название файла
     * @return stdObject
     */
    public function getFile($tableId, $fieldId, $lineId, $fileName) 
    {
        $rawResult = $this->query("/file/" . $tableId . "/" . $fieldId . "/" . $lineId . "/" . $fileName);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }      

    /**
     * Произвольный запрос к API
     * 
     * @param $path string адрес запроса
     * @param $method string метод запроса
     * @param $urlQuery array GET-параметры запроса в виде массива
     * @param $body stdObject данные для запросов POST, PATCH
     * @return stdObject
     */
    public function query($path, $method="GET", $urlQuery = [], $body = null) 
    {
        $requestURL = $this->apiURL . $path;
        
        if ($urlQuery) {
            $urlQueryLine = http_build_query($urlQuery);
            $requestURL .= "?" . $urlQueryLine;
        }

       //echo "<br/><br/>URL: [" . $method . "] " . $requestURL . "<br/><br/>";

        if ($body) {
            $body = json_encode($body);
            //echo $body;
        }

        $out = $this->_sendRequest($requestURL, $method, $body);
        $result = json_decode($out);

        return $result;
    }
    
    /**
     * Преобразование полученных данных в удобный формат
     *
     * @param $rawResult stdObject Данные, полученные по API
     * @return mixed
     * @throws HttpException
     */
    private function _rawToResult($rawResult) 
    {
        $result = null;

        if (!empty($rawResult->data)) {
            $result = $rawResult->data;
            if (is_array($result)) {
                foreach ($result as $k => $item) {
                    $result[$k] = $this->_simpleData($item);
                }
            } else {
                $result = $this->_simpleData($result);
                if (!empty($rawResult->included)) {
                    foreach ($rawResult->included as $includedItem) {
                        $type = $includedItem->type;
                        $itemId = $includedItem->id;
                        if (!isset($result->$type)) {
                            $result->$type = [];
                        }
                        $includedItem = $this->_simpleData($includedItem);
                        $result->$type[$itemId] = $includedItem;
                    }
                }

            }
        }        

        return $result;
    }

    /**
     * Преобразование полученных данных в удобный формат
     *
     * @param $rawResult stdObject Данные, полученные по API
     * @return mixed
     * @throws HttpException
     */    
    private function _simpleData($data) 
    {
        if ($data->meta) {
            foreach ($data->meta as $key => $metaItem) {
                $data->$key = $metaItem;
            }
        }

        return $data;
    }
    
    /**
     * Отправка запроса
     *
     * @param $requestURL string URL обращения к API
     * @param $method string метод запроса
     * @param $body mixed тело запроса
     * @return mixed
     * @throws HttpException
     */
    private function _sendRequest($requestURL, $method="GET", $body=null)
    {
        if ($curl = curl_init()) {

            curl_setopt($curl, CURLOPT_URL, $requestURL);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

            if ($body) {

                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

            }

            $headers = [
                'Content-Type: application/vnd.api+json',
                'X-Auth-Token: ' . $this->token
            ];
            
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $out = curl_exec($curl);
            curl_close($curl);

            return $out;

        } else {
            throw new HttpException('Can not create connection to ' . $requestURL, 404);
        }
    }

}