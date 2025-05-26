<?php
/*
	ファイル　: loginAuth.php
    作成者    :　EI EI KYAW MG
    最終更新日 : 2025/05/23
    概要       : ユーザとパスワードが一致しているかチェックを行う
*/

require_once 'errorMsgs.php'; // エラー返却処理を読み込む
require_once 'mysqlConnect.php'; // DB接続処理を読み込む
require_once 'mysqlClose.php'; // DB切断処理を読み込む

header('Content-Type: application/json');

// ２．Inputパラメータの取得（JSON形式対応）
$input = json_decode(file_get_contents('php://input'), true);
$userId = isset($input['userId']) ? trim($input['userId']) : null;
$password = isset($input['password']) ? trim($input['password']) : null;

// ３．Inputパラメータの必須チェックを行う
if (empty($userId)) {
    getErrorMessage('006'); // 【エラーコード：006】ユーザIDが指定されていません
}
if (empty($password)) {
    getErrorMessage('007'); // 【エラーコード：007】パスワードが指定されていません
}

try {
    // ４．DB接続処理を読み込み、データベースの接続を行う
    global $pdo;

    // ５．送られたユーザIDとパスワードと一致する対象データの件数を取得するSQL文を実行する
    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM user WHERE userId = :userId AND password = :password");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->execute();

    // ６．データのフェッチを行う
    $result = $stmt->fetch();
    if ($result['count'] != 1) {
        getErrorMessage('003'); // 【エラーコード：003】ユーザIDまたはパスワードが違います
    }

    // ７．返却値の連想配列に成功パラメータをセットする
    $response = [
        'status' => 'success',
        'message' => 'ログインに成功しました'
    ];

} catch (PDOException $e) {
    // システムエラーの場合
    error_log("Database error: " . $e->getMessage()); // エラーログに記録
    getErrorMessage('999');
} finally {
    // ８．DB切断処理を呼び出し、データベースの接続を解除する
    closeDatabase(); // mysqlClose.php の関数を直接呼び出す
}

// ９．返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する
echo json_encode($response);
exit;
?>