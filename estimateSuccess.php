<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
	setlocale(LC_MONETARY, 'ja_JP');//通貨表示用
	debug('-------------------------------------------------');
  debug('');
  debug('　見積もり作成完了通知ページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();
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

        見積もり送信が成功しました
          
      </main>

        <!-- <?php require('sidebar-mypage.php'); ?> -->

      </section>


<?php
require('footer.php'); ?>

<!-- <script type="text/javascript" src="jquery-3.4.1.slim.min.js"></script> -->
<script src="drawingDetail.js"></script>

</body>
</html>
