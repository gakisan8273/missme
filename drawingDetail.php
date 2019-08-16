<?php

//共通変数・関数ファイルを読み込み
  require('function.php');
	setlocale(LC_MONETARY, 'ja_JP');//通貨表示用
	debug('-------------------------------------------------');
  debug('');
  debug('　図面詳細ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = '図面詳細';
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

//検討中判別 自分のユーザーIDとこのページの図面IDで検索し、レコードが返ってくるかどうか
$myConsiderDrawings = getMyConsiderDrawings($_SESSION['user_id'], $d_id);

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

//質問掲示板データ取得
$msgData = getMsgDetail($d_id);
debug('掲示板データ一覧：'.print_r($msgData,true));

// 質問掲示板の処理
if(empty($_POST)){
	debug('POSTされていません');
}else{
	debug('POSTされています');
	debug('POST情報：'.print_r($_POST,true));

// バリデーション　空と最大長さ
	$msg = $_POST['msg'];
	validInput($msg,'msg');
	if(empty($err_msg['msg'])){
		validMaxLength($msg,'msg',100);
	}

	if(!empty($err_msg['msg'])){
		debug('バリデーションNG');
	}else{
		debug('バリデーションOK');

		// DBに登録
		try{
			$dbh = dbconnect();
			$sql = 'INSERT INTO message (drawing_id, comment, from_user_id) values(:d_id, :msg, :u_id)';
			$data = array(
				":d_id" => $d_id,
				"msg"   => $msg,
				":u_id" => $_SESSION['user_id'],
			);
			$stmt = queryPost($dbh, $sql, $data);
			if($stmt){
				debug('投稿が成功しました 再読み込みします');
				$url = 'drawingDetail.php?d_id='.$d_id;
				header("Location:".$url);
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
								<?php echo $viewData['work_due_date']; ?>
              </td>

            </tr>

            <tr>
              <th class="table-header">予算</th>
              <td class="drawingDetail-td">
							 <?php echo money_format("%n",$viewData['submit_price']); ?>
							</td>
            </tr>

            <tr>
              <th class="table-header">見積もり回答期限</th>
              <td class="drawingDetail-td">
								<?php echo $viewData['estimate_due_date']; ?>
							</td>
            </tr>

            <tr>
              <th class="table-header">その他コメント</th>
              <td class="drawingDetail-td">
								<?php echo $viewData['detail']; ?>
							</td>
            </tr>
          </table>

					</div>

				
				<?php if( !($viewData['submit_user_id'] === $_SESSION['user_id']) ):?>
					<div class="btn-container">
					<div class="btn-container2">
					<a class="btn estimete-btn form-submit" href="estimate.php?<?php echo appendGetParam(); ?>">見積もりを作成する</a>
					<button type="button" class="estimete-btn form-submit estimate-consider js-click-consider <?php echo ($myConsiderDrawings)? 'consider-active':''?>" data-drawingid = "<?php echo $d_id;?>" ><?php echo ($myConsiderDrawings)? '"検討中"に追加済み':'"検討中"に追加する'?></button>
					</div>
					</div>
				<?php endif?>
				<?php if( ($viewData['submit_user_id'] === $_SESSION['user_id']) ):?>
					<div class="estimate-show">
					<a class="btn estimete-btn form-submit" style="margin:15px auto; float:none;" href="order.php?<?php echo appendGetParam(); ?>">提示された見積もりを見る</a>
					</div>
				<?php endif?>
					</section>
		<!-- 依頼詳細ここまで -->
          
<!-- 発注者情報 -->
        <section class="section-work">
          <div class="mypage-main-section section-work">
            <p class="mypage-section-title mypage-main-section-title">発注者情報</p>
						<table class="table table-profEdit">
            <tr>
              <th class="table-header">会社名</th>
              <td class="drawingDetail-td">
								<?php echo $viewData['company']; ?>
							</td>
            </tr>

            <tr>
              <th class="table-header">担当者名</th>
              <td class="drawingDetail-td">
								<?php echo $viewData['name']; ?>
							</td>
            </tr>

            <tr>
              <th class="table-header">住所</th>
              <td class="drawingDetail-td">
								<?php echo $viewData['address']; ?>
							</td>
						</tr>
						
						<tr>
              <th class="table-header">電話番号</th>
              <td class="drawingDetail-td">
								<?php echo $viewData['tel']; ?>
							</td>
						</tr>

						<tr>
              <th class="table-header">メールアドレス</th>
              <td class="drawingDetail-td">
								<?php echo $viewData['email']; ?>
							</td>
            </tr>

          </table>
					</div>
					</div>

					<?php if( !($viewData['submit_user_id'] === $_SESSION['user_id']) ):?>
					<div class="btn-container">
					<div class="btn-container2">
					<a class="btn estimete-btn form-submit" href="estimate.php?<?php echo appendGetParam(); ?>">見積もりを作成する</a>
					<button type="button" class="estimete-btn form-submit estimate-consider js-click-consider <?php echo ($myConsiderDrawings)? 'consider-active':''?>" data-drawingid = "<?php echo $d_id;?>" ><?php echo ($myConsiderDrawings)? '"検討中"に追加済み':'"検討中"に追加する'?></button>
					</div>
					</div>
					<?php endif?>
        </section>
<!-- 発注者情報ここまで -->

<!-- 質問 -->
        <section class="section-common">
          <div class="mypage-main-section section-common">
						<p class="mypage-section-title mypage-main-section-title">質問 / 回答</p>
						
						<!-- 質問やりとり -->
						<div class="msg-wrapper">
						<?php for($i = 0; $i < count($msgData); $i++){ ?>

							<div class="msg-container">

								<?php if($viewData['submit_user_id'] === $msgData[$i]['id']){?>
									<p class="msg-comment msg-comment-submiter">
										<?php echo $msgData[$i]['comment']?>
									</p>
									<p class="msg-user msg-user-submiter">
										<?php echo $msgData[$i]['company'].'(依頼者)'?>
									</p>
								<?php }else{ ?>
									<p class="msg-user msg-user-worker">
										<?php echo $msgData[$i]['company']?>
									</p>
									<p class="msg-comment msg-comment-worker">
										<?php echo $msgData[$i]['comment']?>
									</p>
								<?php }?><!-- ifの終わり -->

							</div>

						<?php } ?><!-- forの終わり -->

						</div>

						<!-- 投稿 -->

						<form class="" action="" method="post" enctype="multipart/form-data">

          <table class="table table-profEdit">
            <tr>
              <th class="table-header">投稿<br><span class="error-msg table-err"><?php if(!empty($err_msg['msg'])){ echo $err_msg['msg']; } ?></th>
              <td><textarea class="table-input table-input-text" type="text" name="msg" value="" placeholder="質問 / 回答"><?php if(!empty($_POST['msg']) && !empty($err_msg['msg'])) echo $_POST['msg']; ?></textarea></td>
            </tr>
          </table>

          <input class="submit-profEdit" type="submit" name="" value="質問/回答 する">

        </form>
						

          </div>
        </section>

<!-- 質問ここまで -->

      </main>

        <!-- <?php require('sidebar-mypage.php'); ?> -->

      </section>


<?php
require('footer.php'); ?>

</body>
</html>
