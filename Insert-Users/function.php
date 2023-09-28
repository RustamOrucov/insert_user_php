<?php
require_once './users.php';
$fUser = reset($users);

$columns = [];
foreach ($fUser as $key => $value) {
    if (is_int($value) || is_float($value)) {
        $dataType = 'INT';
        $columns[] = "$key $dataType";
    } elseif (is_string($value)) {
        $dataType = 'VARCHAR(255)';
        $columns[] = "$key $dataType";
    } elseif (is_array($value)) {
        foreach ($value as $innerKey => $innerValue) {
            if (is_int($innerValue) || is_float($innerValue)) {
                $innerDataType = 'INT';
            } elseif (is_string($innerValue)) {
                $innerDataType = 'VARCHAR(255)';
            } 
            
            $column = "$key"."_"."$innerKey $innerDataType";
            $columns[] = $column;
        }
    }
}


print_r($columns);

$pdo = new PDO("mysql:host=localhost;dbname=newuser", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//  table creat
$tableName = 'users'; 
$sql = "CREATE TABLE $tableName (" . implode(',', $columns) . ")";

try {
    $pdo->exec($sql);
    echo "table add edildi.\n";
} catch (PDOException $e) {
    echo "table add edilereken xeta: " . $e->getMessage() . "\n";
}

// user leri add et
foreach ($users as $userData) {
    $insertColumns = [];
    $insertValues = [];

    foreach ($userData as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $innerKey => $innerValue) {
                $insertColumns[] = $key . '_' . $innerKey;
                $insertValues[] = is_string($innerValue) ? "'$innerValue'" : $innerValue;
            }
        } else {
            $insertColumns[] = $key;
            $insertValues[] = is_string($value) ? "'$value'" : $value;
        }
    }

    $insertColumnsStr = implode(', ', $insertColumns);
    $insertValuesStr = implode(', ', $insertValues);

    $insertSql = "INSERT INTO $tableName ($insertColumnsStr) VALUES ($insertValuesStr)";

    try {
        $pdo->exec($insertSql);
        echo "user add olundu.\n";
    } catch (PDOException $e) {
        echo "user add olunarken xeta: " . $e->getMessage() . "\n";
    }
}
?>



