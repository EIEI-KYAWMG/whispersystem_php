<?php
/*
    ファイル　：whisperAdd.php
    作成者 : EI EI KYAW MG
    最終変更日 : 2025/05/26
    概要 : ささやき情報をデータベースに挿入処理を行う
*/

//1.エラー返却処理を読み込み
require_once 'errorMsgs.php';

//2.input パラメータの取得
$input = json_decode(file_get_contents('php://input'), true);
$userId = isset($input['userId']) ? trim ($input['userId']) : null;
$content = isset($input['content']) ? trim ($input['content']) : null;

//3.input パラメータの必要チャックを行う
if (empty($userId)) {
    getErrorMessage('006'); // 【エラーコード：006】ユーザIDが指定されていません
}

if(empty($content)){
    getErrorMessage('008'); // 【エラーコード：008】ささやき内容が指定されていません
}

//4.DB接続処理を読み込み、データベースの接続を行う
require_once 'mysqlConnect.php';
global $pdo;
require_once 'mysqlClose.php'; // DB切断処理を読み込み

header('Content-Type: application/json');

try {
    //5.トランザクション処理を開始する
    $pdo->beginTransaction();

    //6.ささやきデータを挿入するSQL文を実行する
    $stmt = $pdo->prepare("INSERT INTO whisper (userId, postDate, content) VALUES (:userId, NOW(), :content)");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);

    if(!$stmt->execute()){
        //6_1.データベースのロールバック処理を実行する
        $pdo->rollBack();

        //6_2.対処エラーメッセージをセットしてエラー終了させる
        getErrorMessage('001'); // 【エラーコード：001】チェック対象：SQL実行結果
    }

    //7.データベースのコミット命令を実行する
    $pdo->commit();

    //8.返却値の連想配列に成功パラメータをセットする
    $response = [
        'result' => 'success' // ← outputパラメータ仕様に合わせる
    ];
}catch(PDOException $e){
    if($pdo->inTransaction()){
        // トランザクション中の場合はロールバック
        $pdo->rollBack();
    }
    getErrorMessage('001'); // 【エラーコード：001】チェック対象：SQL実行結果
}finally{
    //9.DB切断処理を呼び出し、データベースの接続を解除する
    closeDatabase(); // mysqlClose.php の関数を直接呼び出す
}

//10.返却値の連想配列をJSONにエンコードしてoutputパラメータを出力する
echo json_encode($response,JSON_UNESCAPED_UNICODE);
exit; // スクリプトの実行を終了




