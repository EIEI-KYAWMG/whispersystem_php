<?php
/*
	ファイル　: mysqlConnect.php
    作成者  :　増田　碧海
	変更者　: EI EI KYAW MG
    最終更新日 : 2025/05/01
*/

    define("DB_HOST","localhost");
    define("DB_USER","root");
    define("DB_PASSWORD","admin"); // 自分のパスワードに変更
    define("DB_DATABASE","WhisperSystem");

    // PDOオブジェクトの作成
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4"; // 接続情報作成 ※dsn = データソース名(Data Source Name)
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // クエリ実行時のエラーや接続エラーが発生した場合、例外がスローされるよう指定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // 取得した結果を連想配列として取得するフェッチモードの指定
        PDO::ATTR_EMULATE_PREPARES => false,               // プリペアドステートメントをエミュレートしないように設定。これによりSQLインジェクション攻撃からの保護が強化されます。
    ];
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);  // PDOオブジェクト作成
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode()); // 接続エラーが発生した場合、PDOExceptionをスロー
    }

?>