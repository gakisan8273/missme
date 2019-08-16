<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　依頼図面登録ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = '新規依頼';
 ?>

<?php
//ログイン認証
require('auth.php');
?>
<?php
//成功メッセージ表示用
$s_flg = false;
if($_SESSION['s_flg'] === true){;
  $s_flg = true;
  $successMsg = $_SESSION['success'];
  $_SESSION['s_flg'] = false;
  $_SESSION['success'] = '';
}
//DBに接続
try{
  $dbh = dbconnect();
  // カテゴリテーブルのProcessカラムを全て取得
  $sql = 'SELECT * from category';
  $data = array();
  $stmt = queryPost($dbh, $sql,$data);
    if($stmt){
      $categories = $stmt->fetchAll(); //全ての行を取得
      debug('カテゴリ一覧：'.print_r($categories,true));
    }

} catch(Exception $e){

}

  $user_id = $_SESSION['user_id'];
  $post_flg = 0; //読み込み時にフラグをとる
if(!empty($_POST)){
  $post_flg = 1; //POSTされたらフラグを立てる
  //POSTが空でなければ、バリデーション開始
  debug('---------------');
  debug('POSTされました');
  debug('---------------');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));
  // var_dump($_FILES);

  $title = $_POST['title'];
  $drawingNo = $_POST['drawing_no'];
  $category = $_POST['category'];
  $quantity = $_POST['quantity'];
  $hopeMoney = $_POST['hopeMoney'];
  $hopeDueDate = $_POST['hopeDueDate'];
  $estimateDueDate = $_POST['estimateDueDate'];
  $detail = $_POST['detail'];
  $hopeDueDate = $_POST['hopeDueDate'];
  //画像をアップロードし、パスを格納　uploadImgは保存先のパスが返ってくる
  $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'],'pic1'): '';

  //未入力チェック
    // タイトル　個数　希望納期　見積もり期限　図面番号
  validInput($title,'title');
  validInput($drawingNo,'drawing_no');
  validInput($quantity,'quantity');
  validInput($hopeDueDate,'hopeDueDate');
  validInput($estimateDueDate,'estimateDueDate');

  //タイトル　最大文字数
  if(empty($err_msg['title'])){
    validMaxLength($title,'title',255);
  }

  //個数　形式（半角数字）　最大文字数
  if(empty($err_msg['quanity'])){
    validPass($quantity,'quantity'); //パスで代用　これだと半角英数字がOKになる
  }
  if(empty($err_msg['quanity'])){
    validMaxLength($quantity,'quantity',10);
  }

  //希望納期　形式　現在日時よりあと
  //あとで作る

  //見積もり期限　形式　現在日時よりあと
  //あとで作る

  //詳細　最大文字数
  if(empty($err_msg['detail'])){
    validMaxLength($detail,'detail',500);
  }


  debug('エラーメッセージ：'.print_r($err_msg,true));


  if(empty($err_msg)){
    //バリデーション通過
    debug('---------------');
    debug('バリデーションOK');
    debug('---------------');

    //レコードを登録する
    try {
      $dbh = dbConnect();
      $sql = 'INSERT INTO drawings (title, drawing_no, category_id,detail, pic1, submit_user_id, submit_price, estimate_due_date, work_due_date, create_date) VALUES (:title, :d_no, :c_id,:detail, :pic1, :user_id, :price, :e_due_date, :w_due_date, :c_date)';
      $data = array(':title' => $title,':d_no'=>$drawingNo ,':c_id'=>$category['id'] ,':detail' => $detail,':pic1' => $pic1,':user_id' => $_SESSION['user_id'],':price' => $hopeMoney,':e_due_date' => $estimateDueDate, ':w_due_date'=>$hopeDueDate, ':c_date'=>date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);
      if($stmt){
        $_SESSION['success'] = '新規図面の登録に成功しました';
        $_SESSION['s_flg'] = true;
        debug('新規登録成功');
        debug('更新内容：'.print_r($data,true));
        debug('マイページに遷移します');
        header("Location:mypage.php");
        exit();
      }

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
        <form class="" action="" method="post" enctype="multipart/form-data">

          <table class="table table-profEdit">
            <tr>
              <th class="table-header">タイトル<br><span class="error-msg table-err"><?php if(!empty($err_msg['title'])){ echo $err_msg['title']; } ?></th>
              <td>
                <input class="table-input table-input-varchar" type="text" name="title" value="<?php echo getFormData($user_id,'title'); ?>" placeholder="タイトル">
              </td>
              <!-- POSTがあればPOSTを表示、なければDBの値を表示 -->

            </tr>

            <tr>
              <th class="table-header">図面番号<br><span class="error-msg table-err"><?php if(!empty($err_msg['drawing_no'])){ echo $err_msg['drawing_no']; } ?></th>
              <td>
                <input class="table-input table-input-varchar" type="text" name="drawing_no" value="<?php echo getFormData($user_id,'drawing_no'); ?>" placeholder="図面番号">
              </td>
              <!-- POSTがあればPOSTを表示、なければDBの値を表示 -->

            </tr>

            <tr>
              <th class="table-header">カテゴリ<br><span class="error-msg table-err"><?php if(!empty($err_msg['category'])){ echo $err_msg['title']; } ?></th>
              <td>
                <select class="table-input table-input-varchar" type="text" name="category" value="<?php echo getFormData($user_id,'category'); ?>" placeholder="カテゴリ">

                <?php foreach($categories as $category){ ?>
                  <option value="<?php echo $category['id'] ?>"><?php echo $category['process'] ?></option>
                <?php } ?>
              </td>
              <!-- POSTがあればPOSTを表示、なければDBの値を表示 -->

            </tr>

            <tr>
              <th class="table-header">個数<br><span class="error-msg table-err"><?php if(!empty($err_msg['quantity'])){ echo $err_msg['quantity']; } ?></th>
              <td><input class="table-input table-input-varchar" type="text" name="quantity" value="<?php echo getFormData($user_id,'quantity') ?>" placeholder="個数　半角数字のみ"></td>
            </tr>

            <tr>
              <th class="table-header">希望合計金額<br><span class="error-msg table-err"><?php if(!empty($err_msg['hopeMoney'])){ echo $err_msg['hopeMoney']; } ?></span></th>
              <td>
                <input class="table-input table-input-varchar" type="text" name="hopeMoney" value="<?php echo getFormData($user_id,'hopeMoney') ?>" placeholder="希望合計金額　半角数字のみ">
              </td>
            </tr>

            <tr>
              <th class="table-header">希望納期<br><span class="error-msg table-err"><?php if(!empty($err_msg['hopeDueDate'])){ echo $err_msg['hopeDueDate']; } ?></th>
              <td><input class="table-input table-input-varchar" type="date" name="hopeDueDate" value="<?php echo getFormData($user_id,'hopeDueDate') ?>" placeholder="希望納期　yyyy-mm-dd"></td>
            </tr>

            <tr>
              <th class="table-header">回答期限<br><span class="error-msg table-err"><?php if(!empty($err_msg['estimateDueDate'])){ echo $err_msg['estimateDueDate']; } ?></th>
              <td><input class="table-input table-input-varchar" type="date" name="estimateDueDate" value="<?php echo getFormData($user_id,'estimateDueDate') ?>" placeholder="回答期限 yyyy-mm-dd"></td>
            </tr>

            <tr>
              <th class="table-header">詳細<br><span class="error-msg table-err"><?php if(!empty($err_msg['detail'])){ echo $err_msg['detail']; } ?></th>
              <td><textarea class="table-input table-input-text" type="text" name="detail" value="<?php echo getFormData($user_id,'detail') ?>" placeholder="詳細"></textarea></td>
            </tr>

            <!-- 画像をアップロード -->
            <tr>
              <th class="table-header" style="height:250px">図面1<br>ドラッグ＆ドロップ<br><span class="error-msg table-err"><?php if(!empty($err_msg['pic1'])){ echo $err_msg['pic1']; } ?></th>
              <td>
                <div class="input-container">
                  <label class="area-drop" for="input-file">  
                
                    <input type="hidden" name="MAX_FILE_SIZE" value = "3145728">
                    <input type="file" name="pic1" class="input-file" id="input-file">
                    <img src="" alt="" class="prev-img">
                  ドラッグ＆ドロップ
                  </label>
                </div>
              </td>
            
            </tr>



          </table>

          <input class="submit-profEdit" type="submit" name="" value="新規依頼を登録する">

        </form>
      </main>

        <?php require('sidebar-mypage.php'); ?>

      </section>


<?php
require('footer.php'); ?>

  </body>
</html>
