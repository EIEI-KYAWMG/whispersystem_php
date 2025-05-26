<?php

/*
    ファイル   : userWhisperInfo.php
    作成者     : EI EI KYAW MG
    最終更新日 : 2025/05/16
    概要       : 対象ユーザのささやき情報とイイね情報を取得
*/

// １．エラー返却処理を読み込む
require_once 'errorMsgs.php';

// ２．Inputパラメータの取得
$input = json_decode(file_get_contents('php://input'), true);
$userId = isset($input['userId']) ? trim($input['userId']) : null;
$loginUserId = isset($input['loginUserId']) ? trim($input['loginUserId']) : null;

// ３．Inputパラメータの必須チェックを行う
if (empty($userId)) {
    getErrorMessage('006'); // ユーザIDが指定されていません
}
if (empty($loginUserId)) {
    getErrorMessage('015'); // ログインユーザIDが指定されていません
}

try {
    // ４．DB接続処理を呼び出し、データベースの接続を行う
    require_once 'mysqlConnect.php';

    // ５．ユーザ情報を取得するSQL文を実行する
       $stmt = $pdo->prepare("
        SELECT 
            u.userId, 
            u.userName, 
            u.profile,
            IFNULL(fcv.cnt, 0) AS followCount,
            IFNULL(fr.cnt, 0) AS followerCount
        FROM user u
        LEFT JOIN followCntView fcv ON u.userId = fcv.userId
        LEFT JOIN followerCntView fr ON u.userId = fr.followUserId
        WHERE u.userId = :userId
    ");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->execute();

    // ６．データのフェッチを行う
    $user = $stmt->fetch();
    if (!$user) {
        getErrorMessage('004'); // 対象データが見つかりませんでした
    }

    // ７．フォロー中情報を取得するSQL文を実行する
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS cnt
        FROM follow
        WHERE userId = :loginUserId AND followUserId = :userId
    ");
    $stmt->bindParam(':loginUserId', $loginUserId, PDO::PARAM_STR);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->execute();

    // ８．データのフェッチを行う
    $userFollowFlg = $stmt->fetchColumn() > 0 ? true : false;

    // ９．ささやきリストを取得するSQL文を実行する
    $stmt = $pdo->prepare("
        SELECT 
            w.whisperNo,
            w.userId,
            u.userName,
            w.postDate,
            w.content,
            (SELECT COUNT(*) FROM goodInfo g WHERE g.whisperNo = w.whisperNo AND g.userId = :loginUserId) AS goodFlg
        FROM whisper w
        JOIN user u ON w.userId = u.userId
        WHERE w.userId = :userId
        ORDER BY w.postDate DESC
    ");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':loginUserId', $loginUserId, PDO::PARAM_STR);
    $stmt->execute();

    // １０．データのフェッチを行い、検索結果のデータがある間以下の処理を繰り返す
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

    // １１．イイねリストを取得するSQL文を実行する
    $stmt = $pdo->prepare("
        SELECT 
            w.whisperNo,
            w.userId,
            u.userName,
            w.postDate,
            w.content,
            (SELECT COUNT(*) FROM goodInfo g2 WHERE g2.whisperNo = w.whisperNo AND g2.userId = :loginUserId) AS goodFlg
        FROM goodInfo g
        JOIN whisper w ON g.whisperNo = w.whisperNo
        JOIN user u ON w.userId = u.userId
        WHERE g.userId = :targetUserId
        ORDER BY w.postDate DESC
    ");
    $stmt->bindParam(':targetUserId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':loginUserId', $loginUserId, PDO::PARAM_STR);
    $stmt->execute();


    // １２．データのフェッチを行い、検索結果のデータがある間以下の処理を繰り返す
    $goodList = [];
    while ($row = $stmt->fetch()) {
        $goodFlg = ($row['goodFlg'] && $row['goodFlg'] != "0") ? true : false;
        $goodList[] = [
            'whisperNo' => (int)$row['whisperNo'],
            'userId'    => $row['userId'],
            'userName'  => $row['userName'],
            'postDate'  => $row['postDate'],
            'content'   => $row['content'],
            'goodFlg'   => $goodFlg
        ];
    }
    if (count($goodList) === 0) $goodList = null;

    // １３．返却値の連想配列に成功パラメータとユーザ情報、ささやきリスト連想配列、イイねリスト連想配列のデータを格納する
    $response = [
        'result'        => 'success',
        'userId'        => $user['userId'],
        'userName'      => $user['userName'],
        'profile'       => $user['profile'],
        'userFollowFlg' => $userFollowFlg,
        'followCount'   => (int)$user['followCount'],
        'followerCount' => (int)$user['followerCount'],
        'whisperList'   => $whisperList,
        'goodList'      => $goodList
    ]; // ← ここで配列を正しく閉じる

} catch (PDOException $e) {
    echo json_encode([
        'errorCode' => '001',
        'errorMessage' => 'データベース処理が異常終了しました',
        'exceptionMessage' => $e->getMessage()
    ]);
    exit;
} finally {
    require_once 'mysqlClose.php';
    closeDatabase();
}

header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;