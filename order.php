<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
	setlocale(LC_MONETARY, 'ja_JP');//通貨表示用
	debug('-------------------------------------------------');
  debug('');
  debug('　発注ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = '発注';
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

//検討中判別 自分のユーザーIDとこのページの図面IDで検索し、レコードが返ってくるかどうか
// $myConsiderDrawings = getMyConsiderDrawings($_SESSION['user_id'], $d_id);

if( empty($d_id) || empty($viewData) ){
	//d_idがなかったら、一覧ページにとばす
	//d_idが整数以外だったら一覧ページに飛ばす
	// d_idの検索結果がなければ一覧に飛ばす
	debug('図面IDが不正の値です。トップページに遷移します');
	header("Location:index.php");
	exit();
}
	//d_idを元に情報をDBから取得
	// vieadataに入っているので不要
	debug('図面詳細データ抽出成功');
	debug('図面詳細：'.print_r($viewData,true));

// var_dump($viewData['submit_user_id']);
// var_dump($_SESSION['user_id']);

//図面を提示したユーザ名とセッションのユーザ名が一致しなければ、TOPへ飛ばす
if( !($viewData['submit_user_id'] === $_SESSION['user_id']) ){ //両方ともstrになっている
    debug('図面作成者のユーザーIDと閲覧者のユーザーIDが一致しません。トップページに遷移します');
	header("Location:index.php");
	exit();
}

//見積もりデータ取得
$estimateData = getEstimate($d_id);
debug('見積もりデータ一覧：'.print_r($estimateData,true));

// // 発注の処理　見積もりIDを元にDrawingsテーブルに各情報を更新する
if(empty($_POST)){
	debug('POSTされていません');
}else{
	$workDueDate = $_POST['work_due_date'];
	$workPrice = (int)$_POST['submit_price'];
	$workUserId = (int)$_POST['u_id'];
	debug('POSTされています');
	debug('POST情報：'.print_r($_POST,true));

		// DBに登録
		try{
			$dbh = dbconnect();
			$sql = 'UPDATE drawings SET
				work_due_date = :w_date,
				work_price = :w_price,
				work_user_id = :w_u_id 
				where id = :d_id';
			$data = array(
				":d_id" => $d_id,
				":w_date"   => $workDueDate,
				":w_price"  => $workPrice,
				":w_u_id"   => $workUserId,
			);
			$stmt = queryPost($dbh, $sql, $data);
			if($stmt){
				$_SESSION['success'] = '発注が完了しました';
				$_SESSION['s_flg'] = true;
				debug('受注成功しました マイページに遷移します');
				header("Location:mypage.php");
				exit();
			}

		}catch(Exception $e){
			error_log('エラー発生'.$e->getMessage());
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

    <section class="site-width main-containts-wrapper">

      <main class="main-contents-mypage">

        <section class="section-request">

			<!-- 依頼詳細 -->
				<div class="mypage-main-section">

						<p class="mypage-section-title mypage-main-section-title">依頼詳細</p>
						
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

		<table class="table table-profEdit">
            <tr>
              <th class="table-header">希望納期</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($viewData['work_due_date']); ?>
              </td>

            </tr>

            <tr>
              <th class="table-header">予算</th>
              <td class="drawingDetail-td">
							 ¥ <?php echo sanitize(number_format($viewData['submit_price'])); ?>
							</td>
            </tr>

            <tr>
              <th class="table-header">見積もり回答期限</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($viewData['estimate_due_date']); ?>
							</td>
            </tr>

            <tr>
              <th class="table-header">その他コメント</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($viewData['detail']); ?>
							</td>
            </tr>
          </table>                    
        </section>
		<!-- 依頼詳細ここまで -->
          
<!-- 見積もり情報 -->
        <section class="section-work">
          <div class="mypage-main-section section-work">
            <p class="mypage-section-title mypage-main-section-title">見積もり一覧</p>

    <?php foreach($estimateData as $key) :?>
    <div class="estimate-table">
            <table class="table table-profEdit estimate-data">
            <tr>
              <th class="table-header">納期</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($key['work_due_date']); ?>
              </td>

            </tr>

            <tr>
              <th class="table-header">見積もり価格</th>
              <td class="drawingDetail-td">
							 ¥ <?php echo sanitize(number_format($key['estimate_price'])); ?>
							</td>
            </tr>

            <tr>
              <th rowspan="3" class="table-header">その他コメント</th>
              <td rowspan="3" class="drawingDetail-td">
								<?php echo sanitize($key['comment']); ?>
							</td>
            </tr>
            <tr></tr><tr></tr>
          </table>

		<table class="table table-profEdit estimate-company">
            <tr>
              <th class="table-header">会社名</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($key['company']); ?>
							</td>
            </tr>

            <tr>
              <th class="table-header">担当者名</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($key['name']); ?>
							</td>
            </tr>

            <tr>
              <th class="table-header">住所</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($key['address']); ?>
							</td>
						</tr>
						
						<tr>
              <th class="table-header">電話番号</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($key['tel']); ?>
							</td>
						</tr>

						<tr>
              <th class="table-header">メールアドレス</th>
              <td class="drawingDetail-td">
								<?php echo sanitize($key['email']); ?>
							</td>
            </tr>

          </table>
</div>
        <div class="btn-container">
            <div class="btn-container2">
                <form action="" method="post">
										<input type="hidden" name="work_due_date" value="<?php echo sanitize($key['work_due_date']) ?>">
										<input type="hidden" name="submit_price" value="<?php echo sanitize($key['estimate_price']) ?>">
										<input type="hidden" name="u_id" value="<?php echo sanitize($key['u_id']) ?>">
                   <button name="order" type="submit" class="btn estimete-btn form-submit btn-order">↑の見積もりに発注する</button>
                </form>
            </div>
        </div>
<?php endforeach;?>
        </section>
<!-- 発注者情報ここまで -->
      </main>

        <!-- <?php require('sidebar-mypage.php'); ?> -->

      </section>


<?php
require('footer.php'); ?>

<script type="text/javascript" src="jquery-3.4.1.min.js"></script>
<script src="drawingDetail.js"></script>
<script src="considerDrawing.js"></script>
</body>
</html>