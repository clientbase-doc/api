<?php

/**
 * Пример работы с API CRM "Клиентская База"
 * 
 * В скрипте приведены примеры вызова функций API с помощью класса ClientbaseAPI
 * 
 * ВНИМАНИЕ! Скрипт работает с реальными данными вашей CRM.
 * 
 */

    //Подключаем класс для работы с API
    require_once "src/ClientbaseAPI.php";

    //Укажите URL вашей "Клиентской Базы"
    $url = 'http://localhost/distr';

    //Укажите токен, который вы создали для работы с API в настройках "Клиентской Базы"
    $token = "P8O49OmRKgjh8EVmfCSkvKbBtyFiQpVnj0vDzNolr8xLbgdC";
    
    //Создаем объект для работы с API
    $cbAPI = new ClientbaseAPI($url, $token);

?>
<html>
    <head>
        <meta http-equiv="Content-Language" content="ru">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">        
        <title>API CRM "Клиентская База" - пример использования</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"/>
        <link rel="shortcut icon" href="//clientbase.ru/favicon.ico">
        <style>
            h1 {margin: 45px 0px;}
        </style>
    </head>
<body>
<div class="container">
<?

    $action = $_GET['action'];

    switch ($action) {

        case "delete":
            $tableId = $_GET['table_id'];
            $lineId = $_GET['line_id'];
            //Удаляем запись $lineId из таблицы $tableId
            $cbAPI->deleteData($tableId, $lineId);
            break;

        case "save":
            $tableId = $_GET['table_id'];
            $lineId = $_GET['line_id'];
            $data = $_POST['data'];
            //Обновляем запись $lineId в таблице $tableId
            $cbAPI->updateData($tableId, $lineId, $data);            
            break;

        case "add":
            $tableId = $_GET['table_id'];
            $data = $_POST['data'];
            //Добавляем запись в таблицу $tableId
            $cbAPI->addData($tableId, $data);            
            break;            


    }

    $display = $_GET['display'];

    switch ($display) {

        case "table":
            $tableId = $_GET['table_id'];
            //Получаем информацию о таблице $tableId, включая поля таблицы
            $table = $cbAPI->getTable($tableId, true);
            //print_r($table);
            //Получаем последние 10 записей таблицы $tableId
            $tableData = $cbAPI->getDataList($tableId, 0, 10);
            
            echo '<h1>Таблица "' . $table->attributes->table_name . '"</h1>';
            echo '<table class="table">';
            echo '<thead><tr>';
            foreach ($table->fields as $field) {
                if ($field->attributes->view_tb) {
                    echo '<th>' . $field->attributes->field_name . '</th>';
                }
            }
            echo '<td></td><td></td></tr></thead>';
            if ($tableData) {
                echo '<tbody>';
                foreach ($tableData as $data) {
                    echo '<tr>';
                    foreach ($table->fields as $field) {
                        if ($field->attributes->view_tb) {
                            echo '<td>' . $data->attributes->{$field->attributes->int_name} . '</td>';
                        }
                    }
                    echo '<td><a class="btn btn-info" href="?display=edit&table_id=' . $tableId . '&line_id=' . $data->id . '">Изменить</a></td>';
                    echo '<td><a class="btn btn-danger" onClick="if (!confirm(\'Внимание! Вы удаляете данные из таблицы в CRM! Уверены, что хотите продолжить?\')) {return false;}" href="?display=table&table_id=' . $tableId . '&action=delete&line_id=' . $data->id . '">Удалить</a></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
            }
            echo '</table>';
            echo '<br/><br/><p><a class="btn btn-info" href="?display=edit&table_id=' . $tableId . '">Добавить запись</a></p>';
            echo '<br/><br/><p><a href="?">Вернуться к списку таблиц</a></p>';

            break;

        case "edit":
            $tableId = $_GET['table_id'];
            $lineId = $_GET['line_id'];
            //Получаем информацию о таблице $tableId, включая поля таблицы
            $table = $cbAPI->getTable($tableId, true);

            if ($lineId) {
                //Получаем информацию о записи $lineId таблицы $tableId
                $data = $cbAPI->getData($tableId, $lineId);
                $formAction = '?action=save&display=table&table_id=' . $tableId . '&line_id=' . $lineId;
                echo '<h1>Редактирование записи</h1>';            
            } else {
                $data = null;
                $formAction = '?action=add&display=table&table_id=' . $tableId;
                echo '<h1>Добавление записи</h1>';
            }
            
            echo '<form action="' . $formAction . '" method="POST">';
            foreach ($table->fields as $field) {
                if ($field->attributes->view_tb && in_array($field->attributes->field_type, [1,3])) {
                    $label = $field->attributes->field_name;
                    $inputName = 'data[' . $field->attributes->int_name . ']';
                    $inputValue = $data ? $data->attributes->{$field->attributes->int_name} : "";
                    
                    echo '<div class="form-group">';
                    echo '<label>' . $label . '</label>';
                    echo '<div><input type="text" class="form-control" name="' . $inputName . '" value="' . $inputValue . '"/></div>';
                    echo '</div>';
                }
            }      
            echo '<div><input type="submit" value="Сохранить" class="btn btn-info" />';      
            echo '</form>';
            echo '<br/><br/><p><a href="?display=table&table_id=' . $tableId . '">Вернуться к таблице</a></p>';

            break;

        default: 
            //Получаем список пользовательских таблиц
            $tables = $cbAPI->getTablesList();

            echo '<h1>Таблицы</h1><ul>';
            foreach ($tables as $table) {
                echo '<li><a href="?display=table&table_id=' . $table->id . '">' . $table->attributes->table_name . '</a></li>';
            }
            echo '</ul>';


    }

?>
</div>
</body>
</html>