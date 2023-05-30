<?php

class ClientbaseAPI
{
    private $apiURL;
    private $token;

    public function __construct(string $clientbaseURL, string $token)
    {
        if (!$clientbaseURL || !$token) {
            throw new Exception('Clientbase URL or token is empty');
        }

        if (substr($clientbaseURL, -1) !== '/') $clientbaseURL .= '/';
        if (substr($clientbaseURL, 0, 4) !== 'http') {
            switch (substr($clientbaseURL, 0, 2)) {
                case ':/':
                    $clientbaseURL = 'http' . $clientbaseURL;
                    break;

                case '//':
                    $clientbaseURL = 'http:' . $clientbaseURL;
                    break;

                default:
                    $clientbaseURL = 'http://' . $clientbaseURL;
                    break;
            }
        }        

        $this->apiURL = $clientbaseURL . "api/dev";
        $this->token = $token;
    }    
    
    
    /**
     * Получить список пользовательских таблиц
     * 
     * @return array
     */        
    public function getTablesList(): array
    {
        $rawResult = $this->query("/table");
        $result = $this->_rawToResult($rawResult);

        return $result;
    }

    /**
     * Получить информацию о таблице
     * @param $tableId int id таблицы
     * @param $includeFields bool Получить информацию о полях таблицы
     * @return stdClass
     */    
    public function getTable(int $tableId, bool $includeFields = false) : stdClass 
    {
        if ($tableId <= 0) {
            throw new Exception('Incorrect table id: ' . $tableId);
        }

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
    public function getDataList(int $tableId, int $offset=0, int $limit=0, $filter='') : array 
    {
        if ($tableId <= 0) {
            throw new Exception('Incorrect table id: ' . $tableId);
        }

        if ($offset < 0) {
            throw new Exception('Incorrect offset: ' . $offset);
        }

        if ($limit < 0) {
            throw new Exception('Incorrect limit: ' . $limit);
        }

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
     * @return stdClass
     */
    public function getData(int $tableId, int $lineId) : stdClass 
    {
        if ($tableId <= 0) {
            throw new Exception('Incorrect table id: ' . $tableId);
        }
        
        if ($lineId <= 0) {
            throw new Exception('Incorrect line id: ' . $lineId);
        }

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
    public function deleteData(int $tableId, int $lineId) 
    {
        if ($tableId <= 0) {
            throw new Exception('Incorrect table id: ' . $tableId);
        }
        
        if ($lineId <= 0) {
            throw new Exception('Incorrect line id: ' . $lineId);
        }

        $this->query("/data" . $tableId . "/" . $lineId, "DELETE");
    }

    /**
     * Перевести массив с данными в формат, подходящий для отправки на сервер
     * 
     * @param $data array Массив с данными для добавления/обновления записи в таблице
     * @return stdClass
     */
    public function bodyFromData(array $data) : stdClass 
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
     * @return stdClass
     */
    public function addData(int $tableId, array $data) : stdClass
    {
        if ($tableId <= 0) {
            throw new Exception('Incorrect table id: ' . $tableId);
        }
                
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
     * @return stdClass
     */
    public function updateData(int $tableId, int $lineId, array $data) : stdClass 
    {
        if ($tableId <= 0) {
            throw new Exception('Incorrect table id: ' . $tableId);
        }
        
        if ($lineId <= 0) {
            throw new Exception('Incorrect line id: ' . $lineId);
        }

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
    public function getUsersList() : array
    {
        $rawResult = $this->query("/user");
        $result = $this->_rawToResult($rawResult);

        return $result;
    }

    /**
     * Получить информацию о пользователе
     * 
     * @param $userId id пользователя
     * @return stdClass
     */
    public function getUser(int $userId) : stdClass 
    {
        if ($userId <= 0) {
            throw new Exception('Incorrect user id: ' . $userId);
        }

        $rawResult = $this->query("/user/" . $userId);
        $result = $this->_rawToResult($rawResult);

        return $result;
    }    

    /**
     * Получить список групп пользователей
     * 
     * @return array
     */
    public function getGroupsList() : array
    {
        $rawResult = $this->query("/group");
        $result = $this->_rawToResult($rawResult);

        return $result;
    }      

    /**
     * Получить информацию о группе пользователей
     * 
     * @param $groupId id группы пользователей
     * @return stdClass     
     */
    public function getGroup($groupId) : stdClass
    {
        if ($groupId <= 0) {
            throw new Exception('Incorrect group id: ' . $groupId);
        }

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
     * @return stdClass
     */
    public function getFile(int $tableId, int $fieldId, int $lineId, string $fileName) : stdClass
    {
        if ($tableId <= 0) {
            throw new Exception('Incorrect table id:' . $tableId);
        }
        
        if ($lineId <= 0) {
            throw new Exception('Incorrect line id: ' . $lineId);
        }

        if ($fieldId <= 0) {
            throw new Exception('Incorrect field id: ' . $fieldId);
        }
                
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
     * @return stdClass
     */
    public function query(string $path, string $method="GET", array $urlQuery = [], $body = null) : stdClass
    {

        if (substr($path, 0, 1) !== '/') $path = '/' . $path;   
        
        $method = mb_strtoupper($method);
        if (!in_array($method, ['GET', 'POST', 'PATCH', 'DELETE'])) {
            throw new Exception('Incorrect method: ' . $method);
        }

        $requestURL = $this->apiURL . $path;
        
        if ($urlQuery) {
            $urlQueryLine = http_build_query($urlQuery);
            $requestURL .= "?" . $urlQueryLine;
        }

        if ($body) {
            $body = json_encode($body);
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
     * @throws Exception
     */
    private function _rawToResult($rawResult) 
    {
        $result = [];

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
     * @throws Exception
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
     * @throws Exception
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
            throw new Exception('Can not create connection to ' . $requestURL);
        }
    }

}