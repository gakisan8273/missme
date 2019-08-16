<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　パスリマインダーページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = 'パスワード再発行';

//ログイン認証はしない
// require('auth.php');

  //POST送信されていた場合
  if (!empty($_POST['resister'])){
    debug('---------------');
    debug('POSTされました');
    debug('---------------');

    //変数にユーザー情報を代入
    $email = $_POST['email'];

    // 未入力チェック
      validInput($email,'email');

      //メールとパスのバリデーション進捗を分ける

      // メールバリデーション
      if(empty($err_msg['email'])){
        //　形式チェック
        validEmail($email,'email');
      }

      debug('エラーメッセージ：'.print_r($err_msg,true));
      // メール、パス両方でエラーがなければバリデーション通過
      if(empty($err_msg)){
        debug('---------------');
        debug('バリデーションOK');
        debug('---------------');
        //POSTされたメールアドレスとパスワードが一致するか検証
        try{
          $dbh = dbConnect(); //DBへ接続
          $sql = 'SELECT * FROM users WHERE email = :email AND delete_flg = 0';
          $data =array(':email' => $email);
          $stmt = queryPost($dbh, $sql, $data); //クエリ実行結果
          $result = $stmt->fetch(PDO::FETCH_ASSOC); //クエリ結果をresultに格納

          debug('クエリ結果の中身' . print_r($result,true));

          if( !empty($result) && $stmt){
            //EmailがDBに登録されている
            debug('クエリ成功、DB登録あり');

            //認証キー生成
            $authKey = makeRandKey();

            //メール送信
            $from = 'mint.daa.a2@gmail.com';
            $to = $email;
            $subject = 'パスワード再発行認証';
            $comment =<<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーを入力いただくとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：
http://localhost:8888/90_output/05_MissMe/passRemainderInput.php
認証キー：{$authKey}
※認証キーの有効期限は30分です

認証キーを再発行されたい場合は下記ページより再度再発行をお願いします。
http://localhost:8888/90_output/05_MissMe/passRemainder.php

このメールに覚えがない場合、このメールを破棄してください。

EOT;
            sendMail($from, $to, $subject, $comment);

            //認証に必要な情報をセッションに保存
            $_SESSION['auth_key'] = $authKey;
            $_SESSION['auth_limit'] = time()+(60*30); //現在時刻より30分語のUNIXタイムスタンプを入れる
            $_SESSION['auth_email'] = $email;
            debug('セッション変数の中身：'.print_r($_SESSION,true));

            debug('トップページに遷移します');
            header("Location:index.php");
            exit();
      }else{
        debug('クエリに失敗したかDBに登録のないEmailが入力されました');
        $err_msg['common'] = MSG07;
      }
    } catch(Exeption $e){
      error_log('エラー発生；'.$e->getMessage());
      $err_msg['common'] = MSG07;
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
        <p class="remainder-sentence">パスワード再発行のための認証キーとURLを、<br>ご登録されたメールアドレス宛に送信します</p>
        <span class="error-msg error-common"><?php if(!empty($err_msg['common'])){ echo $err_msg['common']; } ?></span>

        <label class="form form-label" for="">メールアドレス</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['email'])){ echo $err_msg['email']; } ?></span>
        <input class="form form-input" type="text" name="email"
        value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>"
         placeholder="メールアドレス">

        <input class="form form-submit" type="submit" name="resister" value="認証キー送信">

      </form>

    </section>

<?php require('footer.php'); ?>

  </body>
</html>
