<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　パスワード再発行ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = 'パスワード再発行';

//ログイン認証はしない
// require('auth.php');

//sessionに認証キーがあるか確認　なければパスリマインダーにリダイレクト
if(empty($_SESSION['auth_key'])){
  debug('セッションに情報がありません');
  debug('パスリマインダー送信画面に遷移します');
  header("Location:passRemainder.php");
}

  //POST送信されていた場合
  if (!empty($_POST['authkey'])){
    debug('---------------');
    debug('POSTされました');
    debug('---------------');

    //変数にユーザー情報を代入
    $authKey = $_POST['authkey'];
    debug('POST情報:'.print_r($_POST,true));

    // 未入力チェック
      validInput($authKey,'authkey');

      debug('エラーメッセージ：'.print_r($err_msg,true));
      // メール、パス両方でエラーがなければバリデーション通過
      if(empty($err_msg)){
        debug('---------------');
        debug('バリデーションOK');
        debug('---------------');
        //POSTされた認証キーとセッションが一致するか検証
        if($authKey !== $_SESSION['auth_key']){
          $err_msg['common'] = MSG15;
        }
        //有効期限がきれていないか
        if(time() > $_SESSION['auth_limit']){
          $err_msg['common'] = MSG16;
        }
        if(empty($err_msg)){
          debug('認証OK');

          //パスワード生成
          $pass = makeRandKey();
          // パスワードを上書き
          try{
            $dbh = dbConnect(); //DBへ接続
            $sql = 'UPDATE users SET password =:pass WHERE email = :email AND delete_flg = 0';
            $data =array(":pass" => password_hash($pass,PASSWORD_DEFAULT),':email' => $_SESSION['auth_email']);
            $stmt = queryPost($dbh, $sql, $data); //クエリ実行結果
            debug(print_r($stmt,true));
            // $result = $stmt->fetch(PDO::FETCH_ASSOC); //クエリ結果をresultに格納　不要なぜならばUPDATEなので検索結果ではないから
            // debug(print_r($result,true));

            // debug('クエリ結果の中身' . print_r($result,true));

            if($stmt){
              //EmailがDBに登録されている
              debug('クエリ成功、DB登録あり');
  
              //メール送信
              $from = 'mint.daa.a2@gmail.com';
              $to = $_SESSION['auth_email'];
              $subject = 'パスワード再発行完了';
              $comment =<<<EOT
パスワードの再発行をいたしました
下記のURLにて再発行パスワードを入力いただきログインしてください。
  
ログインページ：
http://localhost:8888/90_output/05_MissMe/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードの変更をお願いします

このメールに覚えがない場合、このメールを破棄してください。
  
EOT;
              sendMail($from, $to, $subject, $comment);
  
              //セッション削除
              debug('パスワード再発行のメールを送信しました');
              session_destroy();
              debug('セッション変数の中身：'.print_r($_SESSION,true));
  
              debug('トップページに遷移します');
              header("Location:index.php");
              exit();
        }else{
          debug('クエリに失敗しました');
          $err_msg['common'] = MSG07;
        }
      } catch(Exeption $e){
        error_log('エラー発生；'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }

    }else{
      debug('認証エラー'.print_r($err_msg,true));
    }


        
  }
}
  ?>




<?php
require('head.php');
 ?>


  <body>

<?php
require('header.php');
 ?>

    <section class="site-width">

      <form class="login-form" action="" method="post">
        <p class="remainder-sentence">ご指定のメールアドレスにお送りした、パスワード再発行認証キーを入力してください</p>
        <span class="error-msg error-common"><?php if(!empty($err_msg['common'])){ echo $err_msg['common']; } ?></span>

        <label class="form form-label" for="">認証キー</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['authkey'])){ echo $err_msg['authkey']; } ?></span>
        <input class="form form-input" type="text" name="authkey"
        value="<?php if(!empty($_POST['authkey'])) echo $_POST['authkey']; ?>"
         placeholder="認証キー">

        <input class="form form-submit" type="submit" name="btn" value="パスワード再発行">

      </form>

    </section>

<?php require('footer.php'); ?>

  </body>
</html>
