# Класс для работы с API CRM "Клиентская База" и пример использования

### Подключите класс src/ClientbaseAPI для работы с API:

```
$cbAPI = new ClientbaseAPI('[URL вашей CRM]', '[Токен для доступа к API']);
```

### Пример: получить список всех пользовательских таблиц

```
$tables = $cbAPI->getTablesList();

foreach ($tables as $table) {
    echo 'Таблица с id=' . $table->id . ' называется $table->attributes->table_name . '<br/>';
}
```

Примеры решения разных задач Вы можете найти в файле `index.php`.


### Вы можете использовать следующие методы для работы с API:

* getTablesList()


