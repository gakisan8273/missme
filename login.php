<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　ログインページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = 'ログイン';

//ログイン認証
require('auth.php');

  //POST送信されていた場合
  if (!empty($_POST['resister'])){
    debug('---------------');
    debug('POSTされました');
    debug('---------------');
    debug('POST情報：'.print_r($_POST,true));

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    // 未入力チェック
      validInput($email,'email');
      validInput($pass,'pass');

      //メールとパスのバリデーション進捗を分ける

      // メールバリデーション
      if(empty($err_msg['email'])){
        //　形式チェック
        validEmail($email,'email');
      }


      // パスバリデーション
      if(empty($err_msg['pass'])){
        //　形式チェック
          validPass($pass,'pass');
      }

      if(empty($err_msg['pass'])){
        // 形式がOKなら、文字数チェック
          validMinLength($pass,'pass');
          validMaxLength($pass,'pass');
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
          $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
          $data =array(':email' => $email);
          $stmt = queryPost($dbh, $sql, $data); //クエリ実行結果
          $result = $stmt->fetch(PDO::FETCH_ASSOC); //クエリ結果をresultに格納

          debug('クエリ結果の中身' . print_r($result,true));


          if( !empty($result) && password_verify($pass, $result['password'])){
            //ログイン成功
            debug('パスワードがマッチしました');

            //セッション処理

            //ログイン有効期限（デフォルトを1時間とする）
            if(!empty($_POST['extend'])){
              $sesLimit = 60*60*24*30; //チェックされていれば１ヶ月に延長
              debug('ログイン有効期限を延長');
            }else{
              $sesLimit = 60*60; //チェックボックスの有無で時間を変える
              debug('ログイン有効期限延長なし');
            }

            //最終ログイン日時を現在日時にする
            $_SESSION['login_date'] = time();
            // ログイン有効期限をセッションに入力
            $_SESSION['login_limit'] = $sesLimit;
            //ユーザIDをセッションに入力
            $_SESSION['user_id'] = $result['id'];

            debug('セッション変数の中身：' .print_r($_SESSION, true));
            debug('トップページへ遷移します');
            header("Location:index.php");
            exit();

          }else{

          //ログイン失敗
          debug('パスワードがアンマッチです');
          $err_msg['common'] = MSG09;
          }

        } catch (Exeption $e) {
          error_log('エラー発生：' . $e->getMessage());
          $err_msg['common'] = MSG07;
        }



      }
}

//ゲスト１ボタンがPOSTされていれば、ユーザーID１でログイン　パスとアドレス入力なし
if(!empty($_POST['guest-1'])){
  //最終ログイン日時を現在日時にする
  $_SESSION['login_date'] = time();
  $sesLimit = 60*60*24*30;
  // ログイン有効期限をセッションに入力
  $_SESSION['login_limit'] = $sesLimit;
  //ユーザIDをセッションに入力
  $_SESSION['user_id'] = 1;
  debug('セッション変数の中身：' .print_r($_SESSION, true));
  debug('トップページへ遷移します');
  header("Location:index.php");
  exit();
}

if(!empty($_POST['guest-2'])){
  //最終ログイン日時を現在日時にする
  $_SESSION['login_date'] = time();
  $sesLimit = 60*60*24*30;
  // ログイン有効期限をセッションに入力
  $_SESSION['login_limit'] = $sesLimit;
  //ユーザIDをセッションに入力
  $_SESSION['user_id'] = 2;
  debug('セッション変数の中身：' .print_r($_SESSION, true));
  debug('トップページへ遷移します');
  header("Location:index.php");
  exit();
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

      <form class="login-form" action="login.php" method="post">
        <span class="error-msg error-common"><?php if(!empty($err_msg['common'])){ echo $err_msg['common']; } ?></span>

        <label class="form form-label" for="">メールアドレス</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['email'])){ echo $err_msg['email']; } ?></span>
        <input class="form form-input" type="text" name="email"
        value="<?php if(!empty($_POST['email'])) echo sanitize($_POST['email']); ?>"
         placeholder="メールアドレス">

        <label class="form form-label" for="">パスワード</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['pass'])){ echo $err_msg['pass']; } ?></span>
        <input class="form form-input" type="password" name="pass" placeholder="半角英数字6〜10文字" value="<?php if(!empty($_POST['pass'])) echo sanitize($_POST['pass']); ?>">

        <input class="form form-submit" type="submit" name="resister" value="ログイン">

        <input class="form-checkbox" id="login-checkbox" type="checkbox" name="extend" value="1" checked="checked">
        <label class="form form-extend" for="login-checkbox">ログイン状態を保持する</label>

        <a class="kotira" href="passRemainder.php">※パスワードを忘れた方はこちら</a>

      </form>

      <form class="login-form" action="login.php" method="post">
        <input class="form form-submit" type="submit" name="guest-1" value="ゲストユーザー1としてログイン">
      </form>
      <form class="login-form" action="login.php" method="post">
        <input class="form form-submit" type="submit" name="guest-2" value="ゲストユーザー2としてログイン">
      </form>

    </section>

<?php require('footer.php'); ?>

  </body>
</html>
