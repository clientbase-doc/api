# Класс для работы с API CRM "Клиентская База"

**ClientbaseAPI** - это PHP-класс, позволяющий работать с методами API CRM "Клиентская база".

### Начало работы 

Подключите файл `src/ClientbaseAPI.php` к вашему проекту.

Создайте экземпляр класса ClientbaseAPI для работы с API:


```
$cbAPI = new ClientbaseAPI('[URL вашей CRM]', '[Токен для доступа к API']);
```

### Как пользоваться

Пример использования методов класса для получения данных из CRM:

```php
//Получаем список пользовательских таблиц
$tables = $cbAPI->getTablesList();

foreach ($tables as $table) {
    echo 'Таблица с id=' . $table->id . ' называется $table->attributes->table_name . '<br/>';
}
```

Примеры решения разных задач Вы можете найти в файле `index.php`.


### Все методы

#### Таблицы

* _getTablesList()_ - получить список пользовательских таблиц
* _getTable(int $tableId, bool $includeFields=false)_ - получить информацию о таблице, включая информацию о полях

#### Данные в таблицах

* _getDataList(int $tableId, int $offset=0, int $limit=0, mixed $filter='')_ - получить список записей таблицы c ограничением количества и выборкой по фильтру
* _getData(int $tableId, int $lineId)_ - получить информацию о записи в таблице
* _getData(int $tableId, int $lineId)_ - получить информацию о записи в таблице
* _getFile(int $tableId, int $fieldId, int $lineId, string $fileName)_ - получить информацию файле из поля типа "Файл" в записи

* _deleteData(int $tableId, int $lineId)_ - удалить запись из таблицы
* _addData(int $tableId, array $data)_ - добавить запись в таблицу
* _updateData(int $tableId, int $lineId, array $data)_ - обновить запись в таблице

#### Пользователи

* _getUsersList()_ - получить список пользователей
* _getUser(int $userId)_ - получить информацию о пользователе

#### Группы пользователей

* _getGroupsList()_ - получить список групп пользователей
* _getGroup(int $groupId)_ - получить информацию о группе пользователей

#### Запрос к API

* _query(string $path, string $method="GET", array $urlQuery = [], $body = null)_ -   произвольный запрос к API
