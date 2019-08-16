<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
	setlocale(LC_MONETARY, 'ja_JP');//通貨表示用
	debug('-------------------------------------------------');
  debug('');
  debug('　Ajax　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();

//================================
// Ajax処理
//================================

//POSTがあり、ユーザーIDがあり、ログインしていれば処理を実行する(セッションにユーザーIDがあればログイン済みとする)
// issetは0もTrue
debug($_POST['drawingId']);
debug($_SESSION['user_id']);


if( !( isset($_POST['drawingId']) && isset($_SESSION['user_id']) ) ){
    //どちらかがなければ何もしない
}else{
    debug('POST送信があります');
    $d_id = $_POST['drawingId'];
    debug('図面ID：'.print_r($d_id,true));

    try{

    // DB処理　検討中レコードがあるか検索し、
    $dbh = dbconnect();
    $sql = 'SELECT count(*) as resultCount from consider where drawing_id = :d_id and user_id = :u_id';
    $data = array(
        ":d_id" => $d_id,
        ":u_id" => $_SESSION['user_id'],
    );
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetchAll();
    $resultCount = $result[0]['resultCount'];
    // debug($result);
    // debug($resultCount);

    // レコードがあればそれを削除
    if($resultCount){
        debug('レコードが該当しました。削除します');
        $sql = 'DELETE from consider where drawing_id = :d_id and user_id = :u_id';
        $data = array(
            ":d_id" => $d_id,
            ":u_id" => $_SESSION['user_id'],
        );  
        $stmt = queryPost($dbh, $sql, $data);
     // レコードがなければ挿入する
    }else{
        debug('該当レコードがありません。追加します');
        $sql = 'INSERT into consider (drawing_id, user_id, create_date) value(:d_id,:u_id,:c_date)';
        $data = array(
            ":d_id" => $d_id,
            ":u_id" => $_SESSION['user_id'],
            ":c_date" => date('Y-m-d H:i:s')
        );  
        $stmt = queryPost($dbh, $sql, $data);
    }
} catch (Exception $e){
    error_log('エラー発生:' . $e->getMessage());
}

}

debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
 ?>