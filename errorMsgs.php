<?php
/*
	ファイル　: errorMsgs.php
    作成者  :　増田　碧海
	変更者　: EI EI KYAW MG
    最終更新日 : 2025/05/09
*/

header('Content-Type: application/json'); 

// エラーメッセージの定義
$errors = [
    '001' => 'データベース処理が異常終了しました',
    '002' => '変更内容がありません',
    '003' => 'ユーザIDまたはパスワードが違います',
    '004' => '対象データが見つかりませんでした',
    '005' => 'ささやき内容がありません',
    '006' => 'ユーザIDが指定されていません',
    '007' => 'パスワードが指定されていません',
    '008' => 'ささやき管理番号が指定されていません',
    '009' => '検索区分が指定されていません',
    '010' => '検索文字列が指定されていません',
    '011' => 'ユーザ名が指定されていません',
    '012' => 'フォロユーザIDが指定されていません',
    '013' => 'フォローフラグが指定されていません',
    '014' => 'イイねフラグが指定されていません',
    '015' => 'ログインユーザIDが指定されていません',
    '016' => '検索区分が不正です',
    '999' => 'システムエラーが発生しました'
];

// エラーメッセージを取得するメソッド
function getErrorMessage($errorCode) {
    global $errors; // グローバル変数を使用
    if (array_key_exists($errorCode, $errors)) {
        $response = [
            'errorCode' => $errorCode,
            'errorMessage' => $errors[$errorCode]
        ];
    } else {
        $response = [
            'errorCode' => 'unknown',
            'errorMessage' => '不明なエラーコードです'
        ];
    }
    echo json_encode($response); // JSON形式で出力
    exit; // スクリプトの実行を終了
}
