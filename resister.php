<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　ユーザー登録ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = '新規登録';

//POST送信されていた場合
if (!empty($_POST['resister'])){
  debug('---------------');
  debug('POSTされました');
  debug('---------------');

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $repass = $_POST['re-pass'];

  // 未入力チェック
    validInput($email,'email');
    validInput($pass,'pass');
    validInput($repass,'re-pass');

    //メールとパスのバリデーション進捗を分ける

    // メールバリデーション
    if(empty($err_msg['email'])){
      // 重複チェック
      validEmailDup($email,'email');
    }
    if(empty($err_msg['email'])){
      //　重複がなければ、形式チェック
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

    if( empty($err_msg['pass']) && empty($err_msg['re-pass']) ){
      //形式、文字数がOKなら、一致チェック
          validMatch($pass,$repass,'re-pass');
    }

    // メール、パス両方でエラーがなければバリデーション通過
    if(empty($err_msg)){

        //ここまでくればバリデーション通過
          try {
              debug('---------------');
              debug('バリデーションOK');
              debug('---------------');
              $dbh = dbConnect();
              $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:password,:login_time,:create_date)';
              //PASSWORD_DEFAULT -> ハッシュ生成のロジック
              $data = array(':email' => $email,
              ':password' => password_hash($pass,PASSWORD_DEFAULT),
              ':login_time' => date('Y-m-d H:i:s'),
              ':create_date' => date('Y-m-d H:i:s')
            );
              $stmt = queryPost($dbh, $sql, $data);

              //クエリ成功の場合、ログイン処理
              if($stmt){ //$stmtにはtrueが入る？
                //ログイン有効期限　デフォルト1時間とする
                $sesLimit = 60 * 60;
                //最終ログイン日時を現在日時にする
                $_SESSION['login_date'] = time();
                $_SESSION['login_limit'] = $sesLimit;
                //ユーザIDを格納 lastInsertIdで最後に登録したIDを取得
                $_SESSION['user_id'] = $dbh->lastInsertId();//PDOオブジェクトのメソッド

                debug('セッション変数の中身：' .print_r($_SESSION, true));
                debug('トップページへ遷移します');
                header("Location:index.php");
                exit();
              }
          } catch (Exeption $e) {
            error_log('エラー発生：' . $e->getMessage());
            $err_msg['common'] = MSG07;
          }
        }else{
          debug('---------------');
          debug('バリデーションNG email：'. print_r($err_msg['email'],true));
          debug('バリデーションNG pass：'. print_r($err_msg['pass'],true));
          debug('バリデーションNG re-pass：'. print_r($err_msg['re-pass'],true));
          debug('---------------');
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

      <form class="login-form" action="resister.php" method="post">
        <label class="form form-label" for="">メールアドレス</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['email'])){ echo $err_msg['email']; } ?></span>
        <input class="form form-input" type="text" name="email"
        value="<?php if(!empty($_POST['email'])) echo sanitize($_POST['email']); ?>"
         placeholder="メールアドレス">

        <label class="form form-label" for="">パスワード</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['pass'])){ echo $err_msg['pass']; } ?></span>
        <input class="form form-input" type="password" name="pass" placeholder="半角英数字6〜10文字" value="<?php if(!empty($_POST['pass'])) echo sanitize($_POST['pass']); ?>">

        <label class="form form-label" for="">パスワード再入力</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['re-pass'])){ echo $err_msg['re-pass']; } ?></span>
        <input class="form form-input" type="password" name="re-pass" placeholder="パスワード再入力" value="<?php if(!empty($_POST['re-pass'])) echo sanitize($_POST['re-pass']); ?>">

        <input class="form form-submit" type="submit" name="resister" value="新規登録">
      </form>

    </section>

<?php require('footer.php'); ?>

  </body>
</html>
