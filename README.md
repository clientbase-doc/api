# Класс для работы с API 2.0 CRM "Клиентская База"

**ClientbaseAPI** - это PHP-класс, позволяющий работать с методами API 2.0 CRM "Клиентская база".

<a href="https://clientbase.ru/help/for_admin_16/api_2.html" target="_blank">Документация по работе с API 2.0 на сайте clientbase.ru</a> 

## Начало работы 

Подключите файл `src/ClientbaseAPI.php` к вашему проекту.

Создайте экземпляр класса ClientbaseAPI для работы с API:


```
$instance = new ClientbaseAPI('[URL вашей CRM]', '[Токен для доступа к API']);
```

## Как пользоваться

Пример использования методов класса для получения данных из CRM:

```php
//Получаем список пользовательских таблиц
$tables = $instance->getTablesList();

foreach ($tables as $table) {
    echo 'Таблица с id=' . $table->id . ' называется $table->attributes->table_name . '<br/>';
}
```

Примеры решения разных задач Вы можете найти в файле `index.php`.


## Все методы

### Таблицы

#### getTablesList() 

Получить список пользовательских таблиц.

Возвращаемое значение: массив, состоящий из объектов stdObject, содержащих информацию о таблице.

#### getTable(int $tableId, bool $includeFields=false)

Получить информацию о таблице, включая информацию о полях.

Параметры:
* _$tableId_ ID таблицы
* _$includeFields_ Включить в результат информацию о полях таблицы

Возвращаемое значение: объект stdObject, содержащий информацию о таблице.

### Данные в таблицах

* _getDataList(int $tableId, int $offset=0, int $limit=0, mixed $filter='')_ - получить список записей таблицы c ограничением количества и выборкой по фильтру
    *_$tableId_ ID таблицы
    *_$offset_ Отступ от начала списка
    *_$limit_ Количество результатов выборки
    *_$filter_ Условие выборки в формате массива или строки
* _getData(int $tableId, int $lineId)_ - получить информацию о записи в таблице
    *_$tableId_ ID таблицы
    *_$lineId_ ID записи в таблице
* _getFile(int $tableId, int $fieldId, int $lineId, string $fileName)_ - получить информацию файле из поля типа "Файл" в записи
    *_$tableId_ ID таблицы
    *_$fieldId_ ID поля, содержащего файл   
    *_$lineId_ ID записи в таблице
    *_$fileName_ Название файла    
* _deleteData(int $tableId, int $lineId)_ - удалить запись из таблицы
    *_$tableId_ ID таблицы
    *_$lineId_ ID записи в таблице
* _addData(int $tableId, array $data)_ - добавить запись в таблицу
    *_$tableId_ ID таблицы
    *_$data_ массив с данными записи для добавления
* _updateData(int $tableId, int $lineId, array $data)_ - обновить запись в таблице
    *_$tableId_ ID таблицы
    *_$lineId_ ID записи в таблице
    *_$data_ массив с данными записи для обновления    

### Пользователи

* _getUsersList()_ - получить список пользователей
* _getUser(int $userId)_ - получить информацию о пользователе
    *_$userId_ ID пользователя

### Группы пользователей

* _getGroupsList()_ - получить список групп пользователей
* _getGroup(int $groupId)_ - получить информацию о группе пользователей
    *_$groupId_ ID группы пользователей

### Произвольный запрос к API

* _query(string $path, string $method="GET", array $urlQuery = [], $body = null)_ -   произвольный запрос к API
    *_$path_ Путь команды API
    *_$method_ HTTP-метод обращения к API (GET, POST, PATCH, DELETE)
    *_$urlQuery_ Query-параметры запроса в формате массива
    *_$body_ Body запроса
