<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
	setlocale(LC_MONETARY, 'ja_JP');//通貨表示用
	debug('-------------------------------------------------');
  debug('');
  debug('　見積もり作成ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = '見積もり作成';
 ?>

<?php
//ログイン認証
require('auth.php');
 ?>

<?php
//GETでdrawingIDを受け渡し
// DrawingIDから画像URLや依頼ユーザーID、依頼者情報を取得する

//(int)は文字列をint型に変換する
// GETで送信されたものは文字列になっている
// 全角文字、半角英字を(int)すると、int型の0に変換される
// emptyは0をtrue判定する
$d_id = ($_GET['d_id'])? (int)$_GET['d_id'] : ''; 
debug('図面ID：'.print_r($d_id,true));
$viewData = getDrawingDetail($d_id);
debug(print_r($viewData,true));

if( empty($d_id) || empty($viewData) ){
	//d_idがなかったら、一覧ページにとばす
	//d_idが整数以外だったら一覧ページに飛ばす
	// d_idの検索結果がなければ一覧に飛ばす
	debug('図面IDが不正の値です。トップページに遷移します');
	header("Location:index.php");
	exit();

} else{
	//d_idを元に情報をDBから取得
	// vieadataに入っているので不要
	debug('図面詳細データ抽出成功');
	debug('図面詳細：'.print_r($viewData,true));
}

// //質問掲示板データ取得
// $msgData = getMsgDetail($d_id);
// debug('掲示板データ一覧：'.print_r($msgData,true));

// 質問掲示板の処理
if(empty($_POST)){
	debug('POSTされていません');
}else{
	debug('POSTされています');
	debug('POST情報：'.print_r($_POST,true));

	// POSTされるもの
	// work_due_date
	// estimate_price
	// comment

// バリデーション
	$w_dueDate = $_POST['work_due_date'];
	$e_price = $_POST['estimate_price'];
	$comment = $_POST['comment'];

// work_due_date
// 入力必須、形式チェック
	validInput($w_dueDate,'w_dueDate');
	if(empty($err_msg['w_dueDate'])){
		//日付形式チェックを作るはずだった・・・
	}

// estimate_price
// 入力必須、最大長さ、形式チェック
	validInput($e_price,'e_price');
	if(empty($err_msg['e_price'])){
		validMaxLength($e_price,'e_price',8); //１億円いったらエラー
	}
	if(empty($err_msg['e_price'])){
		// 半角数字チェックは省略・・・
	}

// comment
// 最大長さのみ
	validMaxLength($comment,'comment');


	if(!empty($err_msg)){
		debug('バリデーションNG');
	}else{
		debug('バリデーションOK');

		// DBに登録
		try{
			$dbh = dbconnect();
			$sql = 'INSERT INTO estimates (drawing_id, user_id, work_due_date, estimate_price, comment, create_date) values(:d_id, :u_id, :w_due_date, :e_price,:comment,:c_date)';
			$data = array(
				":d_id" => $d_id,
				":u_id" => $_SESSION['user_id'],
				":w_due_date" => $w_dueDate,
				":e_price" => $e_price,
				":c_date"  => date('Y-m-d h:i:s'),
				":comment" => $comment,
			);
			$stmt = queryPost($dbh, $sql, $data);
			if($stmt){
				debug('見積もり送信が成功しました');
				$_SESSION['success'] = '見積もりを提出しました';
        		$_SESSION['s_flg'] = true;
				header("Location:index.php");
				exit();
			}

		}catch(Exception $e){
			error_log('エラー発生'.$e->getMessage());
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

    <section class="site-width main-containts-wrapper">

      <main class="main-contents-mypage">

        <section class="section-request">

				<div class="mypage-main-section">

						<p class="mypage-section-title mypage-main-section-title">見積もり作成</p>
						
            <div class="detail-drawing-container">

                <div class="detail-main-drawing">
                  <img class="detail-main-img js-switch-img-main" src="<?php echo $viewData['pic1']; ?>">
                </div>

                <div class="detail-sub-drawing">
                  <img class="detail-sub-img js-switch-img-sub" src="<?php echo $viewData['pic1']; ?>" alt="">
                  <img class="detail-sub-img js-switch-img-sub" src="<?php echo $viewData['pic2']; ?>" alt="">
                  <img class="detail-sub-img js-switch-img-sub" src="<?php echo $viewData['pic3']; ?>" alt="">
                </div>
						</div>

						<form class="" action="" method="post" enctype="multipart/form-data">
            <table class="table table-profEdit">
            <thead>
                <th class="table-header">項目</th>
                <th class="drawingDetail-td th-right-line">依頼元希望</th>
                <th class="drawingDetail-td">見積もり内容</th>
            </thead>
            <tr>
              <th class="table-header">希望納期</th>
              <td class="drawingDetail-td th-right-line">
								<?php echo $viewData['work_due_date']; ?>
              </td>
              <td class="drawingDetail-td">
								<input class="table-input table-input-varchar" type="date" name="work_due_date" value="<?php echo getFormData($user_id,'work_due_date') ?>" placeholder="納期">
              </td>

            </tr>

            <tr>
              <th class="table-header">予算</th>
              <td class="drawingDetail-td th-right-line">
                ¥ <?php echo number_format($viewData['submit_price']); ?>
							</td>
							<td class="drawingDetail-td">
							<input class="table-input table-input-varchar" type="text" name="estimate_price" value="<?php echo getFormData($user_id,'estimate_price') ?>" placeholder="見積もり価格">
              </td>
            </tr>

            <tr>
              <th class="table-header">その他コメント</th>
              <td class="drawingDetail-td th-right-line">
								<?php echo $viewData['detail']; ?>
							</td>
							<td class="drawingDetail-td">
							<textarea class="table-input table-input-text" type="text" name="comment" value="<?php echo getFormData($user_id,'comment') ?>" placeholder="その他コメント"></textarea>
              </td>
            </tr>
					</table>
					
					<input class="btn estimete-btn form-submit" type="submit" value="見積もりを送信する">

					</form>


					</div>

					</section>
		<!-- 依頼詳細ここまで -->
          
      </main>

        <!-- <?php require('sidebar-mypage.php'); ?> -->

      </section>


<?php
require('footer.php'); ?>

<!-- <script type="text/javascript" src="jquery-3.4.1.slim.min.js"></script> -->
<script src="drawingDetail.js"></script>

</body>
</html>
