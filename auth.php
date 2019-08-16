<?php

//================================
// ログイン認証・自動ログアウト
//================================
// ログインしている場合

//セッションにユーザーIDが入っていれば、ログイン済みと判定する
if( !empty($_SESSION['user_id'])){
  debug('ログイン済みです');

  //現在日時が最終ログイン日時＋有効期限を超えていた場合
  if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time() ){
    debug('ログイン有効期限オーバーです');
    //セッションを削除＝ログアウトする
    session_destroy();

    //topページかログインページでなければ、ログインページに遷移
    if(!( basename($_SERVER['PHP_SELF']) === 'index.php' || basename($_SERVER['PHP_SELF']) === 'login.php') ){
      debug('ログインページへ遷移します');
      header("Location:login.php");
      exit();
    }

  }else{

    //現在日時が有効期限以内の場合
    debug('ログイン有効期限以内です');
    //最終ログイン日時を現在日時に更新
    $_SESSION['login_date'] = time();
    debug('セッション変数の中身'.print_r($_SESSION,true));
  }

//ログインしていない場合
}else{

  debug('ログインしていません');
  debug('セッション変数の中身'.print_r($_SESSION,true));

  //topページかログインページでなければ、ログインページに遷移
  if(!( basename($_SERVER['PHP_SELF']) === 'index.php' || basename($_SERVER['PHP_SELF']) === 'login.php') ){
    debug('ログインページへ遷移します');
    header("Location:login.php");
    exit();
  }
}
 ?>
