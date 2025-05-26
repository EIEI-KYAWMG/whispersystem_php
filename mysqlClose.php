<?php
/*
	ファイル　: mysqlClose.php
    作成者  :　増田　碧海
	変更者　: EI EI KYAW MG
    最終更新日 : 2025/05/01
*/

header('Content-Type: application/json');

function closeDatabase() {
    global $pdo; // グローバル変数を使用
    $pdo = null; // PDO接続を切断
}
