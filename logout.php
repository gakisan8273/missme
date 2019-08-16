<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　ログアウトページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();

debug('ログアウトします');
//セッションを削除＝ログアウト
session_destroy();//サーバ側のセッションIDとセッション変数を削除する
  // session_unsetはセッション変数は削除されるがセッションIDは残る

debug('セッション変数の中身：'.print_r($_SESSION,true));
debug('ログインページへ遷移します');
//トップページへ
header("Location:login.php");
