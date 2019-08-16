<?php

//共通変数・関数ファイルを読込み
require('function.php');
debug('-------------------------------------------------');
debug('');
debug('　退会ページ　');
debug('');
debug('-------------------------------------------------');
debugLogStart();
$pageTitle = '退会';

//ログイン認証
require('auth.php');

?>

<!-- //POST送信されていれば -->
<?php
if(!empty($_POST)){
	debug('POST送信があります');

	try{
		$dbh = dbConnect();
		$sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :user_id';
		$sql2 = 'UPDATE product SET delete_flg = 1 WHERE id = :user_id';
		$sql3 = 'UPDATE consider SET delete_flg = 1 WHERE id = :user_id';
		$data = array(":user_id" => $_SESSION['user_id']);
		$stmt1 = queryPost($dbh,$sql1,$data);
		$stmt2 = queryPost($dbh,$sql2,$data);
		$stmt3 = queryPost($dbh,$sql3,$data);

		if($stmt1){
			//セッションを削除する
			session_destroy();
			debug('退会処理をしました');
			debug('セッション変数の中身：'.print_r($_SESSION,true));
			debug('トップページに遷移します。');
			header("Location:index.php");
			exit();

		}else{
			debug('クエリが失敗しました');
			$err_msg['common'] = MSG07;
		}

	} catch(Exception $e){
		error_log('エラー発生：'.$e->getMessage());
		$err_msg['common'] = MSG07;
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

 <form class="login-form" action="withdraw.php" method="post">
   
   <input class="form form-submit" type="submit" name="withdraw" value="退会する">


 </form>


</section>



<?php
require('footer.php'); ?>

  </body>
</html>
