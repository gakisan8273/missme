<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　プロフィール編集ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = 'プロフィール編集';
 ?>

<?php
//ログイン認証
require('auth.php');
?>
<?php

  $user_id = $_SESSION['user_id'];
  $post_flg = 0; //読み込み時にフラグをとる
if(!empty($_POST)){
  $post_flg = 1; //POSTされたらフラグを立てる
  //POSTが空でなければ、バリデーション開始
  debug('---------------');
  debug('POSTされました');
  debug('---------------');

  $company = $_POST['company'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $tel = $_POST['tel'];
  $address = $_POST['address'];

  //会社名
  validMaxLength($company,'company',255);

  // 代表者名
  validMaxLength($name,'name',255);

  // メール
  validInput($email,'email');
  if(empty($err_msg['email'])){
    //　形式チェック
    validEmail($email,'email');
  }

  // 電話
  validMaxLength($tel,'tel',11);
  //エラーなく、かつ電話番号がPOSTされていれば
  if(empty($err_msg['email']) && !empty($tel) ) {
    //　形式チェック
    validTel($tel,'tel');
  }


  // 住所
  validMaxLength($address,'address',255);

  //空欄で送信されたものがあれば、NULLに変更する
  ChangeEmptyIntoNull($company,'company');
  ChangeEmptyIntoNull($name,'name');
  ChangeEmptyIntoNull($tel,'tel');
  ChangeEmptyIntoNull($address,'address');

  debug('エラーメッセージ：'.print_r($err_msg,true));


  if(empty($err_msg)){
    //バリデーション通過
    debug('---------------');
    debug('バリデーションOK');
    debug('---------------');

    //レコードを更新する
    try {
      $dbh = dbConnect();
      $sql = 'UPDATE users SET company = :company, name = :name, email = :email,  tel = :tel, address = :address WHERE id = :id';
      $data = array(':company' => $company,':name' => $name,':email' => $email,':tel' => $tel,':address' => $address,':id' => $_SESSION['user_id']);
      $stmt = queryPost($dbh, $sql, $data);
      debug('プロフィール更新成功');
      debug('更新内容：'.print_r($data,true));
      $_SESSION['success'] = 'プロフィールを更新しました';
        $_SESSION['s_flg'] = true;
      debug('マイページに遷移します');
      header("Location:mypage.php");
      exit();

    } catch(Exception $e){

    }


  }else{
    debug('---------------');
    debug('バリデーションNG');
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

    <section class="site-width main-containts-wrapper">

      <main class="main-contents-mypage">
        <form class="" action="" method="post">

          <table class="table table-profEdit">
            <tr>
              <th class="table-header">会社名<br><span class="error-msg table-err"><?php if(!empty($err_msg['company'])){ echo $err_msg['company']; } ?></th>
              <td>
                <input class="table-input table-input-varchar" type="text" name="company" value="<?php echo getFormData($user_id,'company') ?>" placeholder="会社名">
              </td>
              <!-- POSTがあればPOSTを表示、なければDBの値を表示 -->

            </tr>

            <tr>
              <th class="table-header">代表者名<br><span class="error-msg table-err"><?php if(!empty($err_msg['name'])){ echo $err_msg['name']; } ?></th>
              <td><input class="table-input table-input-varchar" type="text" name="name" value="<?php echo getFormData($user_id,'name') ?>" placeholder="代表者名"></td>
            </tr>

            <tr>
              <th class="table-header">メールアドレス<br><span class="error-msg table-err"><?php if(!empty($err_msg['email'])){ echo $err_msg['email']; } ?></span></th>
              <td>
                <input class="table-input table-input-varchar" type="text" name="email" value="<?php echo getFormData($user_id,'email') ?>" placeholder="メールアドレス">
              </td>
            </tr>

            <tr>
              <th class="table-header">電話番号<br><span class="error-msg table-err"><?php if(!empty($err_msg['tel'])){ echo $err_msg['tel']; } ?></th>
              <td><input class="table-input table-input-varchar" type="text" name="tel" value="<?php echo getFormData($user_id,'tel') ?>" placeholder="電話番号"></td>
            </tr>

            <tr>
              <th class="table-header">住所<br><span class="error-msg table-err"><?php if(!empty($err_msg['address'])){ echo $err_msg['address']; } ?></th>
              <td><input class="table-input table-input-text" type="text" name="address" value="<?php echo getFormData($user_id,'address') ?>" placeholder="住所"></td>
            </tr>

          </table>

          <input class="submit-profEdit" type="submit" name="" value="プロフィール編集を実行">

        </form>
      </main>

        <?php require('sidebar-mypage.php'); ?>

      </section>


<?php
require('footer.php'); ?>

  </body>
</html>
