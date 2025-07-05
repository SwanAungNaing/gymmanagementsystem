<?php
function insertData($table, $mysqli, $data)
{
    $columns = [];
    $values = [];
    foreach ($data as $key => $val) {
        $columns[] = "`" . $key . "`";
        $values[] = "'" . $val . "'";
    }
    $column = implode(', ', $columns);
    $value = implode(', ', $values);
    $sql = "INSERT INTO `$table` 
            ($column)
            VALUES 
            ($value)";
    return $mysqli->query($sql);
}

function selectData($table, $mysqli, $column = "*", $where = "", $order = "")
{
    $sql = "SELECT $column FROM `$table` $where $order";
    return $mysqli->query($sql);
}
function deleteData($table, $mysqli, $where)
{
    $sql = "DELETE FROM `$table` WHERE $where";
    return $mysqli->query($sql);
}

