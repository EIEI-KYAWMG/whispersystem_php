<?php

/*
    ファイル   : timelineInfo.php
    作成者     : EI EI KYAW MG
    最終更新日 : 2025/05/23
    概要       : 対象ユーザがフォローしているささやきを取得する
*/


require_once 'errorMsgs.php';
require_once 'mysqlConnect.php';
global $pdo;
require_once 'mysqlClose.php';

header('Content-Type: application/json');

// 2. Inputパラメータの取得
$input = json_decode(file_get_contents('php://input'), true);
$userId = isset($input['userId']) ? trim($input['userId']) : null;

// 3. Inputパラメータの必須チェック
if (empty($userId)) {
    getErrorMessage('006'); // ユーザIDが指定されていません
}

try {
    // 5. ささやきリストの内容を取得するSQL文を実行
    $sql = "
        SELECT 
            w.whisperNo,
            w.userId,
            u.userName,
            w.postDate,
            w.content,
            (SELECT COUNT(*) FROM goodInfo g WHERE g.whisperNo = w.whisperNo AND g.userId = :userId1) AS goodFlg
        FROM whisper w
        JOIN user u ON w.userId = u.userId
        WHERE w.userId = :userId2
        UNION
        SELECT 
            w.whisperNo,
            w.userId,
            u.userName,
            w.postDate,
            w.content,
            (SELECT COUNT(*) FROM goodInfo g WHERE g.whisperNo = w.whisperNo AND g.userId = :userId3) AS goodFlg
        FROM whisper w
        JOIN user u ON w.userId = u.userId
        WHERE w.userId IN (
            SELECT followUserId FROM follow WHERE userId = :userId4
        )
        ORDER BY postDate DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId1', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':userId2', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':userId3', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':userId4', $userId, PDO::PARAM_STR);
    $stmt->execute();

    // 6. データのフェッチ
    $whisperList = [];
    while ($row = $stmt->fetch()) {
        $goodFlg = ($row['goodFlg'] && $row['goodFlg'] != "0") ? true : false;
        $whisperList[] = [
            'whisperNo' => (int)$row['whisperNo'],
            'userId'    => $row['userId'],
            'userName'  => $row['userName'],
            'postDate'  => $row['postDate'],
            'content'   => $row['content'],
            'goodFlg'   => $goodFlg
        ];
    }
    if (count($whisperList) === 0) $whisperList = null;

    // 7. 返却値の連想配列
    $response = [
        'result'      => 'success',
        'whisperList' => $whisperList
    ];
} catch (PDOException $e) {
    echo json_encode([
        'result'  => 'error',
        'errCode' => '001',
        'errMsg'  => 'データベース処理が異常終了しました',
        'exceptionMessage' => $e->getMessage()
    ]);
    exit;
} finally {
    closeDatabase();
}

// 8. 返却
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;