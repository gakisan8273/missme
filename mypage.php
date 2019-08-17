<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　マイページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
  $pageTitle = 'MyPage';
 ?>

<?php
setlocale(LC_MONETARY, 'ja_JP');//通貨表示用
//ログイン認証
require('auth.php');
//成功メッセージ表示用
$s_flg = false;
if($_SESSION['s_flg'] === true){;
  $s_flg = true;
  $successMsg = $_SESSION['success'];
  $_SESSION['s_flg'] = false;
  $_SESSION['success'] = '';
}
$myDrawings = getMyDrawings($_SESSION['user_id']);
$myConsiderDrawings = getMyConsiderDrawings($_SESSION['user_id']);
$myOrderedDrawings = getMyOrderedDrawings($_SESSION['user_id']);
$myEstimatedDrawings = getMyEstimate($_SESSION['user_id']);
$myWorkDrawings = getMyWorkDrawings($_SESSION['user_id']);

debug(print_r($myEstimatedDrawings,true));

 ?>

<?php
require('head.php');
 ?>

  <body>

<?php
require('header.php');
 ?>
<!-- モーダルウィンドウ -->
<!-- セッションに成功フラグがあればクラスを追加する　このクラスの有無で画面表示発火させる -->
<div id="overlay" class='<?php echo ($s_flg === true)? 's_flg' : ''?>'></div>
<div id="modalWindow">
  <p class="modalWindowMsg"><?php echo $successMsg;?></p>
</div>
    <section class="site-width main-containts-wrapper">

      <main class="main-contents-mypage">

        <section class="section-request">
          <div class="mypage-main-section">
            <p class="mypage-section-title mypage-main-section-title">依頼側情報</p>
            <div class="mypage-sub-section">

                <p class="mypage-section-title mypage-sub-section-title section-request">登録図面一覧</p>
                  <div class="main-panel-list">
                  <?php if(empty($myDrawings)): ?>
                  登録図面はありません
                  <?php else: ?>
                  <?php foreach($myDrawings as $key1): ?>
                    <a class="main-panel" href="drawingDetail.php?d_id=<?php echo $key1['id']; ?>">
                      <div class="panel-head">
                        <img class="panel-img" src="<?php echo $key1['pic1']; ?>" alt="">
                      </div>
                      <div class="panel-body">
                        <p class="panel-title"><?php echo sanitize($key1['title']); ?></p>
                        <p class="panel-price">希望価格：¥ <?php echo sanitize(number_format($key1['submit_price'])); ?></p>
                        <p class="panel-duedate">希望納期：<?php echo sanitize($key1['work_due_date']); ?></p>
                      </div>
                    </a>
                  <?php endforeach ;?>
                  <?php endif;?>
                   </div>

              <p class="mypage-section-title mypage-sub-section-title section-request">発注済み図面一覧</p>
              <div class="main-panel-list">
              <?php if(empty($myOrderedDrawings)): ?>
                  発注中の図面はありません
                  <?php else: ?>
              <?php foreach($myOrderedDrawings as $key1): ?>
                <a class="main-panel" href="drawingDetail.php?d_id=<?php echo $key1['id']; ?>">
                  <div class="panel-head">
                    <img class="panel-img" src="<?php echo $key1['pic1']; ?>" alt="">
                  </div>
                  <div class="panel-body">
                    <p class="panel-title"><?php echo sanitize($key1['title']); ?></p>
                    <p class="panel-price">発注価格：¥ <?php echo sanitize(number_format($key1['work_price'])); ?></p>
                    <p class="panel-duedate">納品期日：<?php echo sanitize($key1['work_due_date']); ?></p>
                  </div>
                </a>
              <?php endforeach;?>
<?php endif;?>
              </div>


            </div>

          </div>
        </section>

        <section class="section-work">
          <div class="mypage-main-section section-work">
            <p class="mypage-section-title mypage-main-section-title">請負側情報</p>
            <div class="mypage-sub-section">
              <p class="mypage-section-title mypage-sub-section-title section-work">請負検討中一覧</p>
              <div class="main-panel-list">
              <?php if(empty($myConsiderDrawings)): ?>
                  検討しているものはありません
                  <?php else: ?>
              <?php foreach($myConsiderDrawings as $key1): ?>
                    <a class="main-panel" href="drawingDetail.php?d_id=<?php echo $key1['id']; ?>">
                      <div class="panel-head">
                        <img class="panel-img" src="<?php echo $key1['pic1']; ?>" alt="">
                      </div>
                      <div class="panel-body">
                        <p class="panel-title"><?php echo sanitize($key1['title']); ?></p>
                        <p class="panel-price">希望価格：¥ <?php echo sanitize(number_format($key1['submit_price'])); ?></p>
                        <p class="panel-duedate">希望納期：<?php echo sanitize($key1['work_due_date']); ?></p>
                      </div>
                    </a>
<?php endforeach;?>
<?php endif;?>
              </div>

              <p class="mypage-section-title mypage-sub-section-title section-work">見積もり提出中一覧</p>
              <div class="main-panel-list">
              <?php if(empty($myEstimatedDrawings)): ?>
                  見積もり提出中図面はありません
                  <?php else: ?>
              <?php foreach($myEstimatedDrawings as $key1): ?>
                    <a class="main-panel" href="drawingDetail.php?d_id=<?php echo $key1['d_id']; ?>">
                      <div class="panel-head">
                        <img class="panel-img" src="<?php echo $key1['pic1']; ?>" alt="">
                      </div>
                      <div class="panel-body">
                        <p class="panel-title"><?php echo sanitize($key1['title']); ?></p>
                        <p class="panel-price">見積価格：¥ <?php echo sanitize(number_format($key1['estimate_price'])); ?></p>
                        <p class="panel-duedate">希望納期：<?php echo sanitize($key1['work_due_date']); ?></p>
                      </div>
                    </a>
<?php endforeach;?>
<?php endif;?>
              </div>
              <p class="mypage-section-title mypage-sub-section-title section-work">受注中一覧</p>
              <div class="main-panel-list">
              <?php if(empty($myWorkDrawings)): ?>
                  受注中図面はありません
                  <?php else: ?>
              <?php foreach($myWorkDrawings as $key1): ?>
                    <a class="main-panel" href="drawingDetail.php?d_id=<?php echo $key1['id']; ?>">
                      <div class="panel-head">
                        <img class="panel-img" src="<?php echo $key1['pic1']; ?>" alt="">
                      </div>
                      <div class="panel-body">
                        <p class="panel-title"><?php echo sanitize($key1['title']); ?></p>
                        <p class="panel-price">請負価格：¥ <?php echo sanitize(number_format($key1['work_price'])); ?></p>
                        <p class="panel-duedate">納品期日：<?php echo sanitize($key1['work_due_date']); ?></p>
                      </div>
                    </a>
<?php endforeach;?>
<?php endif;?>
              </div>
            </div>
          </div>
        </section>


      </main>

        <?php require('sidebar-mypage.php'); ?>

      </section>


<?php
require('footer.php'); ?>

  </body>
</html>
