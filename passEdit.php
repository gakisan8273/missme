<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　パスワード変更ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = 'パスワード変更';

//ログイン認証
require('auth.php');

  //POST送信されていた場合
  if (!empty($_POST['passEdit'])){
    debug('---------------');
    debug('POSTされました');
    debug('---------------');

    //変数にユーザー情報を代入
    $currentPass = $_POST['currentPass'];
    $newPass = $_POST['newPass'];
    $re_newPass = $_POST['re-newPass'];


    // 未入力チェック
      validInput($currentPass,'currentPass');
      validInput($newPass,'newPass');

    // 形式チェック
    if(empty($err_msg['currentPass'])){
        //　形式チェック
          validPass($currentPass,'currentPass');
      }
      if(empty($err_msg['currentPass'])){
        // 形式がOKなら、文字数チェック
          validMinLength($currentPass,'currentPass');
          validMaxLength($currentPass,'currentPass');
      }
      //現在のパスワードがあっているか確認

      if(empty($err_msg['correntPass'])){
        debug('---------------');
        debug('現在のパス　バリデーションOK');
        debug('---------------');
        //POSTされたメールアドレスとパスワードが一致するか検証
        try{
          $dbh = dbConnect(); //DBへ接続
          $sql = 'SELECT password, id FROM users WHERE id = :user_id AND delete_flg = 0';
          $data =array(':user_id' => $_SESSION['user_id']);
          $stmt = queryPost($dbh, $sql, $data); //クエリ実行結果
          $result = $stmt->fetch(PDO::FETCH_ASSOC); //クエリ結果をresultに格納

          debug('クエリ結果の中身' . print_r($result,true));

          if( !empty($result) && password_verify($currentPass, $result['password'])){
            //パスワード一致確認
            debug('パスワードがマッチしました');
          }else{
              debug('パスワードがマッチしません');
              $err_msg['currentPass'] = MSG09;
          }
          //クエリ成功の場合
          if($stmt){
            //メール送信処理
          }

        } catch (Exeption $e) {
            error_log('エラー発生：' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }

      // 新しいパスワード形式チェック
      if(empty($err_msg['newPass'])){
        //　形式チェック
        validPass($newPass,'newPass');
      }
      if(empty($err_msg['newPass'])){
        // 形式がOKなら、文字数チェック
          validMinLength($newPass,'newPass');
          validMaxLength($newPass,'newPass');
      }

      if( empty($err_msg['newPass']) && empty($err_msg['re-newPass']) ){
        //形式、文字数がOKなら、一致チェック
            validMatch($newPass,$re_newPass,'re-newPass');
      }

      debug('エラーメッセージ：'.print_r($err_msg,true));
      // 現在のパス、新しいパス両方でエラーがなければバリデーション通過
      if(empty($err_msg)){
        debug('---------------');
        debug('バリデーションOK');
        debug('---------------');
        //パスワードを更新
        try{
          $dbh = dbConnect(); //DBへ接続
          $sql = 'UPDATE users SET password = :pass WHERE id = :user_id AND delete_flg = 0';
          $data =array(':pass' => password_hash($newPass,PASSWORD_DEFAULT),':user_id' => $_SESSION['user_id']);
          $stmt = queryPost($dbh, $sql, $data); //クエリ実行結果
          $result = $stmt->fetch(PDO::FETCH_ASSOC); //クエリ結果をresultに格納

          debug('クエリ結果の中身' . print_r($result,true));
          $_SESSION['success'] = 'パスワードの更新に成功しました';
        $_SESSION['s_flg'] = true;
          debug('パスワードを更新しました');
          debug('トップページに遷移します');
          header("location:index.php");

        }catch(Exception $e){
            error_log('エラー発生：' . $e->getMessage());
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

    <form class="login-form" action="passEdit.php" method="post">
        <label class="form form-label" for="">現在のパスワード</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['currentPass'])){ echo $err_msg['currentPass']; } ?></span>
        <input class="form form-input" type="password" name="currentPass"
        value="<?php if(!empty($_POST['currentPass'])) echo $_POST['currentPass']; ?>"
         placeholder="現在のパスワード">

        <label class="form form-label" for="">新しいパスワード</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['newPass'])){ echo $err_msg['newPass']; } ?></span>
        <input class="form form-input" type="password" name="newPass" placeholder="半角英数字6〜10文字" value="<?php if(!empty($_POST['newPass'])) echo $_POST['newPass']; ?>">

        <label class="form form-label" for="">新しいパスワード再入力</label>
        <span class="error-msg error-email"><?php if(!empty($err_msg['re-newPass'])){ echo $err_msg['re-newPass']; } ?></span>
        <input class="form form-input" type="password" name="re-newPass" placeholder="新しいパスワード再入力" value="<?php if(!empty($_POST['re-newPass'])) echo $_POST['re-newPass']; ?>">

        <input class="form form-submit" type="submit" name="passEdit" value="パスワード変更">
      </form>

    </section>

<?php require('footer.php'); ?>

  </body>
</html>