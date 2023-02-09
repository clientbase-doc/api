# Класс для работы с API CRM "Клиентская База"

**ClientbaseAPI** - это PHP-класс, позволяющий работать с методами API CRM "Клиентская база".

### Начало работы 

Подключите файл `src/ClientbaseAPI.php` к вашему проекту.

Создайте экземпляр класса ClientbaseAPI для работы с API:


```
$cbAPI = new ClientbaseAPI('[URL вашей CRM]', '[Токен для доступа к API']);
```

### Как пользоваться

```
$tables = $cbAPI->getTablesList();

foreach ($tables as $table) {
    echo 'Таблица с id=' . $table->id . ' называется $table->attributes->table_name . '<br/>';
}
```

Примеры решения разных задач Вы можете найти в файле `index.php`.


### Все методы

* **getTablesList()** - получить список пользовательских таблиц
* **getTable(int $tableId, bool $includeFields=false)** - получить информацию о таблице, включая информацию о полях
* **getDataList(int $tableId, int $offset=0, int $limit=0, mixed $filter='')** - получить список записей таблицы c ограничением количества и выборкой по фильтру
* **getData(int $tableId, int $lineId)** - получить информацию о записи в таблице
* **getData(int $tableId, int $lineId)** - получить информацию о записи в таблице
* **getFile(int $tableId, int $fieldId, int $lineId, string $fileName)** - получить информацию файле из поля типа "Файл" в записи
* **deleteData(int $tableId, int $lineId)** - удалить запись из таблицы
* **addData(int $tableId, array $data)** - добавить запись в таблицу
* **updateData(int $tableId, int $lineId, array $data)** - обновить запись в таблице
* **getUsersList()** - получить список пользователей
* **getUser(int $userId)** - получить информацию о пользователе
* **getGroupsList()** - получить список групп пользователей
* **getGroup(int $groupId)** - получить информацию о группе пользователей
* **query(string $path, string $method="GET", array $urlQuery = [], $body = null)** -   произвольный запрос к API



