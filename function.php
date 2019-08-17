<?php
//================================
// ログ
//================================
//ログを取るか
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log', 'php.log');

ini_set('display_errors','off');
error_reporting(E_ALL & ~E_NOTICE);

//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true; //リリースした後はfalseにする　ログが溢れるので
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){ //empty関数は、true = 1 , false = 0とみなし、0はemptyとみなす
    //error_log関数->エラーメッセージをファイルに送信する
    error_log('デバッグ：' . $str);
  }
}

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ');
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ');
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ');
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ');
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示開始');
  debug('セッションID：' . session_id()); //session_id()はPHPで定義済み
  debug('セッション変数の中身：'.print_r($_SESSION,true));//falseだと情報を表示、Trueだと情報を返す
  debug('現在日時タイムスタンプ' .time()); //現在時刻を Unix エポック (1970 年 1 月 1 日 00:00:00 GMT) からの通算秒として返します
  // セッションにログイン日時とログイン期限が格納されていれば、ログイン期限を吐く
  if(!empty($_SESSION['login_date'] && $_SESSION['login_limit'])){
    debug('ログイン期限日時タイムスタンプ'. ($_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

//================================
// セッション準備・セッション有効期限を延ばす
//================================
// セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない　/tmp/だと早く削除される
session_save_path("/var/tmp/");
// ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ100分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を伸ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
// セッションを使う　セッションの設定はsession_startの前にする
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();


//================================
// 定数
//================================
//エラーメッセージを定数に設定

define('MSG01','入力必須です'); //メール、パス、再入力の未入力チェック用
define('MSG02','Emailの形式で入力してください'); //メール形式チェック
define('MSG03','パスワード（再入力）が一致しません;'); //パスワード再入力チェック
define('MSG04','半角英数字のみご利用いただけます');//パスワード、再入力形式チェック
define('MSG05','6文字以上で入力してください;'); //パス最小文字数チェック
define('MSG06','10文字以内で入力してください'); //パス最大文字数チェック
define('MSG07','エラーが発生しました しばらく経ってからやり直してください'); //DB接続失敗など
define('MSG08','そのメールアドレスはすでに登録されています'); //メール重複チェック
define('MSG09','メールアドレスまたはパスワードが違います');
define('MSG10', '電話番号の形式が違います');
define('MSG11', '郵便番号の形式が違います');
define('MSG12', '古いパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');
define('MSG16', '有効期限が切れています');
define('MSG17', '半角数字のみご利用いただけます');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');
define('SUC05', '購入しました！相手と連絡を取りましょう！');

// /================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
$err_msg = array();



// /================================
// データベース関連
//================================


//クエリ実行　function.php
function queryPost($dbh, $sql, $data){
  //$dbhはdbConnectの返り値として持たせておく
  $stmt = $dbh -> prepare($sql);
  if(!($stmt -> execute($data))){
    debug('クエリに失敗しました');
    debug('失敗したSQL：' . print_r($stmt,true));
    debug('失敗したdata：'. print_r($data,true));
    $err_msg['common'] = MSG07;
    return 0; //失敗したら0を返す ここでreturnなので処理終了
  }
  debug('クエリ成功');
  // debug('クエリ文：'.print_r($stmt,true));
  return $stmt; //成功したら、クエリ文を返す
}


//データベース接続　function.php
function dbConnect(){
  $db = parse_url($_SERVER['CLEARDB_DATABASE_URL']);
  $db['dbname'] = ltrim($db['path'], '/');
  $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";
  $user = $db['user'];
  $password = $db['pass'];
  // $options = array(
  //   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  //   PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  //   PDO::MYSQL_ATTR_USE_BUFFERED_QUERY =>true,
  // )

  // $dsn = 'mysql:dbname=190716_missme;host=localhost;charset=utf8';
  // $user = 'root';
  // $password = 'root';
  $options = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    PDO::ATTR_EMULATE_PREPARES=>false //エミュレートモードをOFFにし、MySQLでの型変換を無効にする
  );
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}

//ユーザーデータ取得
function getUserData($user_id){
  debug('ユーザー情報を取得します');
  debug('対象ユーザーID：'.print_r($user_id,true));
  //db接続
  try{
    $dbh = dbConnect();
    //該当するユーザーIDの全てのカラムを取得
    $sql = 'SELECT * FROM users WHERE id = :id AND delete_flg =0';
    $data = array(':id' => $user_id);
    $stmt = queryPost($dbh, $sql, $data); //クエリ成功ならクエリ文が、失敗なら0が帰ってくる

    if($stmt){
      //ユーザー情報を取得し、返す
      // debug('クエリ文：'.print_r($stmt,true));
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false; //queryPostが失敗していたら
    }

  } catch(Exception $e){
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}



//================================
// バリデーション関数
//================================

//メール形式チェック　function.php
function validEmail($str,$key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
    debug(print_r($key,true).'メール形式NG');
  }else{
  debug(print_r($key,true).'メール形式OK');
  }
}

//未入力チェック　function.php
function validInput($str,$key){
  if(empty($str)){
    global $err_msg; //グローバル変数を使う宣言
    $err_msg[$key] = MSG01; //グローバル変数の[key]にエラーメッセージを格納
    debug('未入力：'.print_r($key,true));
  }else{
    debug('入力済：'.print_r($key,true));
  }
}

//メール重複チェック　function.php
function validEmailDup($str, $key){

  global $err_msg;
  try {
    // DBへ接続するときはtry catchする
    $dbh = dbConnect();
    // SQL分作成
    $sql = 'SELECT email FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $str);

    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($result)){
      $err_msg[$key] = MSG08;
      debug('メールアドレス重複');
    }else{
      debug('メールアドレス重複無し');
    }
  } catch (Exeption $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}



//パスワード形式チェック　function.php
function validPass($str,$key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg; //グローバル変数を使う宣言
    $err_msg[$key] = MSG04; //グローバル変数の[key]にエラーメッセージを格納
    debug('パス形式NG');
  }else{
  debug('パス形式OK');
  }
}

//最小文字数チェック　function.php
function validMinLength($str,$key,$min=6){
  if(mb_strlen($str) < $min){
    global $err_msg; //グローバル変数を使う宣言
    $err_msg[$key] = MSG05; //グローバル変数の[key]にエラーメッセージを格納
    debug('最小文字数NG：'.print_r($key,true));
  }else{
  debug('最小文字数OK：'.print_r($key,true));
  }
}
//最大文字数チェック　function.php
function validMaxLength($str,$key,$max=10){
  if(mb_strlen($str) > $max){
    global $err_msg; //グローバル変数を使う宣言
    $err_msg[$key] = MSG06; //グローバル変数の[key]にエラーメッセージを格納
    debug('最大文字数NG：'.print_r($key,true));
  }else{
  debug('最大文字数OK：'.print_r($key,true));
  }
}

//同値チェック　function.php
function validMatch($str1,$str2,$key){
  if($str1 !== $str2){
    global $err_msg; //グローバル変数を使う宣言
    $err_msg[$key] = MSG03; //グローバル変数の[key]にエラーメッセージを格納
    debug('パスと再入力が違います');
  }else{
  debug('パスと再入力同値');
  }
}
//電話番号形式チェック
function validTel($str, $key){
  if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
    debug('電話番号の形式が違います');
  }else{
  debug('電話番号形式OK');
  }
}

//空欄をNULLに変換する
function ChangeEmptyIntoNull($str,$key){
  if(empty($str)){
    debug(print_r($key,true).'が空欄なのでNULLに変換');
    $str = NULL;
    if(is_null($str)){
      debug(print_r($key,true).':NULL');
    }else{
      debug(print_r($key,true).':NULL変換失敗');
    }
  }
  return $str;
}

//固定長チェック
function validLength($str, $key, $len = 8){
  if( mb_strlen($str) !== $len ){
    global $err_msg;
    $err_msg[$key] = $len . MSG14;
  }
}

//================================
// その他
//================================

//フォーム入力保持
function getFormData($user_id,$str,$flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST; //デフォルトはPOST
  }

//ユーザー情報を取得
  $userData = getUserData($user_id);
  // debug(print_r($userData,true));
//POST情報を取得
  $method[$str];
//POSTされたもののエラー情報を取得
  $err_msg[$str];

  debug(print_r($method[$str],true));
  // var_dump($method[$str]);
  //POSTがあったら
  global $post_flg;
  debug('POSTフラグ：'.print_r($post_flg,true));
  if($post_flg) {
    return $method[$str]; //POSTされた値を保持
  }
  //POSTがなかったら
  //DBもNullだったら
  if(is_null($userData[$str])){
    return; //何も返さない　（空欄のまま）
  //DBに値があったら　
  }else{
    return $userData[$str]; //DBの値を返す　最初に開いたときはこれ
  }
}

//認証キー生成
function makeRandKey($length = 8){
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $str = '';
  // var_dump($chars[61]);
  for ($i = 0; $i < $length; $i++){
    $str .= $chars[mt_rand(0,61)];
  }
  return $str;
}

//メール送信
function sendMail($from, $to, $subject, $comment){
  if(! ( empty($to) || empty($subject) || empty($comment) ) ){
    //文字化けしないように設定（お決まりパターン）
  mb_language("Japanese");
  mb_internal_encoding("UTF-8");

  // メールを送信（送信結果はtrueかfalseで帰ってくる
  $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
    //送信結果を判定
  if($result){
    debug('メールを送信しました　送信先：'.print_r($to,true));
  }else{
    debug('【エラー発生】メールの送信に失敗しました');
  }

  }else
  debug('送信先かタイトルか本文が空です　メール送信失敗');
}

//画像処理
function uploadImg($file, $key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));//$fileには$_FILES['pic1']が入る

  if(isset($file['error']) && is_int($file['error']) ){
    try{
      // バリデーション
      // $file['error']の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている
      // これらの定数はphpでファイルアップロード時に自動的に定義される。
      // 定数には値として０や１などの数値が入っている
      switch ($file['error']){
        case UPLOAD_ERR_OK: //OK
          break;
        case UPLOAD_ERR_NO_FILE: //ファイル未選択の場合
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズが超過した場合
        case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズが超過した場合;
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default: //その他の場合
          throw new RuntimeException('その他のエラーが発生しました');
      }
      
      // $file['mine']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
      // exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_PNG」などの定数を返す
      $type = @exif_imagetype($file['tmp_name']);
      if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){
         //第三引数にtrueを指定すると厳密に型までチェックするので必ずつける
        throw new RuntimeException('画像形式が未対応です');
      }
 
      //ファイルデータからSHA-1ハッシュをとってファイル名を決定し、ファイルを保存する
      // ハッシュ化しておかないとアップロードされたファイル名そのままで保存してしまう、同じファイル名がアップロードされる可能性があり、
      // DBにパスを保存した場合にどっちの画像のパスなのか判断がつかなくなってしまう
      // image_type_to_extension関数はファイルの拡張子を取得するもの
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      if(!move_uploaded_file($file['tmp_name'], $path)){ //ファイルを移動する
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }

      //保存したファイルパスのパーミッション（権限）を変更する
      chmod($path, 0644);
      // 所有者に読み込み、書き込みの権限を与え、その他には読み込みだけ許可する。

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.print_r($path,true));
      return $path;

    }catch(Exception $e){
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();

    }
  }
}
//図面一覧取得
function getDrawingList($currentMinNum = 1, $category, $sort, $span = 30, $keyWord){
debug('図面一覧を取得します');
//図面一覧をDBから取得する
try{
  $dbh = dbconnect();
  //件数用のSQL文作成
  $sql = 'SELECT count(*) as allRecords from drawings';
  if(!empty($category)){
    $sql .= ' WHERE category_id =' .$category;
  }
  if(!empty($keyWord)){
    debug('検索キーワード：'.print_r($keyWord,true));
    $sql .= ' WHERE title like' .'"%'.$keyWord.'%"';
  }
  if(!empty($sort)){
    switch($sort){
      case 1:
        $sql .= ' ORDER BY work_due_date ASC';
        break;
      case 2:
        $sql .= ' ORDER BY work_due_date DESC';
        break;
      case 3:
        $sql .= ' ORDER BY submit_price ASC';
        break;
      case 4:
        $sql .= ' ORDER BY submit_price DESC';
    }
  }
  $data = array();
  debug('SQL : '.$sql);
  $stmt = queryPost($dbh, $sql, $data); //クエリ文が返っている
  // debug(print_r($stmt,true));
  $result = $stmt->fetchAll();
  // debug(print_r($result,true));
  // debug(print_r($result[0]['allRecords'],true));
  $rst['total'] = $result[0]['allRecords']; //総レコード数
  debug('総レコード数：'.print_r($rst['total'],true));
  $rst['total_page'] = (int)ceil($rst['total']/$span); //ceilは切り上げ 総ページ数
  debug('合計ページ数：'.print_r($rst['total_page'],true));

  if(!$stmt){return false;}

  //ページング用のSQL文作成
  $sql = 'SELECT * from drawings';
  if(!empty($category)){
    $sql .= ' WHERE category_id =' .$category;
  }
  if(!empty($keyWord)){
    $sql .= ' WHERE title like' .'"%'.$keyWord.'%"';
  }
  if(!empty($sort)){
    switch($sort){
      case 1:
        $sql .= ' ORDER BY work_due_date ASC';
        break;
      case 2:
        $sql .= ' ORDER BY work_due_date DESC';
        break;
      case 3:
        $sql .= ' ORDER BY submit_price ASC';
        break;
      case 4:
        $sql .= ' ORDER BY submit_price DESC';
    }
  }
  $sql .= ' LIMIT '.$span. ' OFFSET ' .$currentMinNum;
  $data = array();
  debug('SQL : '.$sql);
  $stmt = queryPost($dbh, $sql, $data);

  if($stmt){
    //クエリ結果のデータ全てを格納
    $rst['data'] = $stmt->fetchAll();
    return $rst;
  }else{
    return false;
  }


}catch(Exception $e){
  error_log('エラー発生:' . $e->getMessage());
}
}

// GETパラメータ（URLパラメータ）付与
function appendGetParam($arr_del_key = array()){
  if(!empty($_GET)){
    // debug('GETパラメータ：'.print_r($_GET,true));
    // $str = '?';
    $str = '&';
    foreach($_GET as $key => $val){
      if(!in_array($key, $arr_del_key,true)){
        //取り除きたいパラメータじゃない場合に、urlにくっつけるパラメータを生成
        $str .= $key. '=' .$val. '&';
        // debug('str：'.print_r($str,true));
      }
    }
    //最後nに&がついてしまうので、切り取る　−1をmb_strlen($str)-1に変える必要ある？→なさそう　どちらも同じ結果になった
    $str = mb_substr($str, 0, -1, "UTF-8");
    debug('付与するGETパラメータ：'.print_r($str,true));
    return $str;
  }
}

// ページング
function pagenation( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
//現在ページ数が合計ページ数を超えていたら（表示件数を１５から４５に切り替えたときなど）
// 現在ページ＝合計ページとする
($currentPageNum > $totalPageNum)? $currentPageNum = $totalPageNum : '';


//   //totalPageNum（合計ページ数）が５以上ならば
  if($totalPageNum >= 5){
    debug('ページング計算開始');
    debug('現在ページ数：'.print_r($currentPageNum,true));
    debug('合計ページ数：'.print_r($totalPageNum,true));

    //     　・現在位置が最初のページ　かつ　最後のページが５以上　ならば、一番左から１２３４５　最後＝現在＋４　最初＝現在＝１
    if($currentPageNum === 1){
      $minPageNun = 1;
      $maxPageNum = $currentPageNum + 4; //5
      debug(1);

// 　・現在位置が最後のページ　かつ　最後のページが５以上　ならば、５６７８９（最後）　最初＝現在−４　最後＝現在
    }elseif($currentPageNum === $totalPageNum){
      $minPageNun = $currentPageNum - 4; //totalPageNum - 4
      $maxPageNum = $currentPageNum; //=totalPageNum
      debug(2);
// 　・現在位置が最初から2番目　かつ　最後のページが５以上　ならば　１２３４５　　最初＝現在−１＝１　最後＝現在＋３
    }elseif($currentPageNum === 2){
      $minPageNun = 1;
      $maxPageNum = $currentPageNum + 3; //5
      debug(3);
// 　・現在位置が最後から2番目　かつ　最後のページが５以上　ならば　５６７８９　最初＝現在−３　最後＝現在＋１
    }elseif($currentPageNum === $totalPageNum - 1){
      $minPageNun = $currentPageNum - 3; //totalPageNum - 4
      $maxPageNum = $currentPageNum + 1; //=totalPageNum
      debug(4);
// 　・現在位置がそれ以外　かつ　最後のページが５以上　ならば　４５６７８　最初＝現在−２　最後＝現在＋２
    }else{
      $minPageNun = $currentPageNum - 2;
      $maxPageNum = $currentPageNum + 2;
      debug(5);
    }

//totalPageNumが５未満ならば すべてminPageNum=1 maxPageNum = totalPageNumになる
  }else{
    $minPageNun = 1;
    $maxPageNum = $totalPageNum;
    debug(6);
// //   ・現在位置が最初のページ　ならば　最初＝現在＝１　最後＝最大
//     if($currentPageNum === 1){
//       $minPageNun = 1;
//       $maxPageNum = $totalPageNum;

// // 　・現在位置が最後のページ　ならば　最後＝最大＝現在　最初＝１（上と同じ）
//     }elseif($currentPageNum === $totalPageNum){
//       $minPageNun = 1;
//       $maxPageNum = $totalPageNum;
    
// // 　・現在位置が最初から2番目　ならば　最初＝１　最後＝最大（上と同じ）
//     }elseif($currentPageNum === 2){
//       $minPageNun = 1;
//       $maxPageNum = $totalPageNum;
//     } 
  }
  return [$minPageNun, $maxPageNum];
}

//図面詳細情報取得
function getDrawingDetail($d_id){
  try{
    $dbh = dbconnect();
    $sql = 'SELECT d.title, d.drawing_no, d.detail, d.pic1, d.pic2, d.pic3, d.submit_user_id, d.submit_price, d.estimate_due_date, d.work_due_date, u.id, u.name, u.address, u.tel, u.company, u.email, m.comment, m.from_user_id, m.send_date from drawings as d left join users as u on d.submit_user_id = u.id left join message as m on d.id = m.drawing_id where d.id =:d_id and u.delete_flg = 0';
    $data = array(':d_id' => $d_id);
    $stmt = queryPost($dbh, $sql, $data);
    //querypostで失敗なら0が返ってくる

    if($stmt){
      //クエリ結果のデータを１レコード返却　1個しか結果ないはずなので
      debug('図面詳細データを返します');
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      debug('図面が見つかりません');
      return false;
    }

  }catch(Exception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();   
  }
}
//掲示板詳細情報取得
function getMsgDetail($d_id){
  try{
    $dbh = dbconnect();
    $sql = 'SELECT u.id, u.company, m.comment, m.send_date from drawings as d left join message as m on d.id = m.drawing_id left join users as u on m.from_user_id = u.id where d.id =:d_id and u.delete_flg = 0';
    $data = array(':d_id' => $d_id);
    $stmt = queryPost($dbh, $sql, $data);
    //querypostで失敗なら0が返ってくる

    if($stmt){
      //クエリ結果のデータを全て
      debug('掲示板データを返します');
      return $stmt->fetchAll();
    }else{
      debug('掲示板データが見つかりません');
      return false;
    }

  }catch(Exception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();   
  }
}

//自分の図面一覧を取得
function getMyDrawings($u_id){
  try{
    $dbh = dbconnect();
    $sql = 'SELECT d.id, d.pic1, d.title, d.submit_price, d.work_due_date from drawings as d left join users as u on d.submit_user_id = u.id where u.id =:u_id and u.delete_flg = 0';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);
    //querypostで失敗なら0が返ってくる

    if($stmt){
      //クエリ結果のデータを全て
      debug('自分の図面データを返します');
      return $stmt->fetchAll();
    }else{
      debug('自分の図面データが見つかりません');
      return false;
    }

  }catch(Exception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();   
  }
}

//検討中図面一覧取得
function getMyConsiderDrawings($u_id, $d_id = ''){
  debug('図面一覧を取得します');
  //図面一覧をDBから取得する
  try{
    $dbh = dbconnect();
    $sql = 'SELECT d.id, d.pic1, d.title, d.submit_price, d.work_due_date from drawings as d left join consider as c on d.id = c.drawing_id where c.user_id =:u_id and c.delete_flg = 0';
    $data = array(':u_id' => $u_id);
    
    if(!empty($d_id)){
      $sql .= ' and d.id = :d_id';
      $data = array(':u_id' => $u_id, ":d_id" => $d_id);
    }
    $stmt = queryPost($dbh, $sql, $data);
    //querypostで失敗なら0が返ってくる

    if($stmt){
      //クエリ結果のデータを全て
      debug('自分の検討中図面を返します');
      return $stmt->fetchAll();
    }else{
      debug('自分の検討中図面が見つかりません');
      return false;
    }

  }catch(Exception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();   
  }
}

//見積もり情報取得
function getEstimate($d_id){
  try{
    $dbh = dbconnect();
    $sql = 'SELECT e.work_due_date, e.estimate_price, e.id, e.comment, e.update_date, u.id as u_id, u.name, u.address, u.tel, u.company, u.email from drawings as d left join estimates as e on d.id = e.drawing_id left join users as u on e.user_id = u.id where d.id = :d_id';
    $data = array(':d_id' => $d_id);
    $stmt = queryPost($dbh, $sql, $data);
    //querypostで失敗なら0が返ってくる
    $result = $stmt->fetchAll();

    if(!($stmt) || empty(array_filter($result[0])) ){
      debug('見積もりが見つかりません');
      return false;
    }else{
      //クエリ結果のデータを全て返却
      debug('見積もり一覧を返します');
      return $result;
    }

  }catch(Exception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();   
  }
}

//発注済み図面一覧取得
function getMyOrderedDrawings($u_id){
  debug('発注済み図面一覧を取得します');
  //図面一覧をDBから取得する
  try{
    $dbh = dbconnect();
    $sql = 'SELECT d.id, d.pic1, d.title, d.work_price, d.work_due_date, d.work_user_id from drawings as d left join users as u on d.work_user_id = u.id where d.work_user_id is not null and u.delete_flg = 0 and d.submit_user_id = :u_id';
    $data = array(':u_id' => $u_id);
    
    $stmt = queryPost($dbh, $sql, $data);
    //querypostで失敗なら0が返ってくる

    if($stmt){
      //クエリ結果のデータを全て
      debug('発注済み図面を返します');
      return $stmt->fetchAll();
    }else{
      debug('発注済み図面が見つかりません');
      return false;
    }

  }catch(Exception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();   
  }
}

//自分が提示した見積もり情報取得
function getMyEstimate($u_id){
  try{
    $dbh = dbconnect();
    $sql = 'SELECT d.id as d_id, d.title, d.pic1, e.work_due_date, e.estimate_price, e.id, e.comment, e.update_date, u.id as u_id from drawings as d left join estimates as e on d.id = e.drawing_id left join users as u on e.user_id = u.id where u.id = :u_id';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);
    //querypostで失敗なら0が返ってくる
    $result = $stmt->fetchAll();

    if(!($stmt) || empty(array_filter($result[0])) ){
      debug('見積もりが見つかりません');
      return false;
    }else{
      //クエリ結果のデータを全て返却
      debug('見積もり一覧を返します');
      return $result;
    }

  }catch(Exception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();   
  }
}

//自分が受注した図面情報取得（見積もりを出して、相手が発注したら受注とみなす
function getMyWorkDrawings($u_id){
  try{
    $dbh = dbconnect();
    $sql = 'SELECT d.id, d.title, d.pic1, d.work_due_date, d.work_price from drawings as d left join users as u on d.work_user_id = u.id where u.id = :u_id';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);
    //querypostで失敗なら0が返ってくる
    $result = $stmt->fetchAll();

    if(!($stmt) || empty(array_filter($result[0])) ){
      debug('受注中図面が見つかりません');
      return false;
    }else{
      //クエリ結果のデータを全て返却
      debug('受注中図面一覧を返します');
      return $result;
    }

  }catch(Exception $e){
    debug($e->getMessage());
    global $err_msg;
    $err_msg[$key] = $e->getMessage();   
  }
}

function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES); //シングル・ダブルクォートを共に変換する

}