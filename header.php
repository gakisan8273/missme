<header>
  <div class="site-width header-container">

    <div class="header-left">
      <div class="logo-container">
        <a href="index.php" class="logo logo-small logo-small1">ものづくりの、明日を支える。</a>
        <div class="logo-set">
          <img class="logo-img" src="img/logo.png" alt="">
          <a href="index.php" class="logo logo-big">MissMe-GAKI</a>
        </div>
        <a href="index.php" class="logo logo-small logo-small2">生産材コマース</a>
      </div>
      <div class="sentence-container">
        <p class="header-sentence">
          <span class="header-color">2,070万</span>点、<span class="header-color">3,324</span>メーカーの品揃え、1個から<span class="header-color">送料無料</span>。<br>
          18時までのご注文で最短<span class="header-color">当日出荷</span>可能。
        </p>
        <p class="header-info">
          現時刻のご注文は翌営業日のお取り扱いとなります。<br>
          営業時間は8:00~20:00です。（日曜・年末年始を除く）
        </p>
      </div>




      <form class="header-finding" action="" method="get">
        <input class="header-finding-area" type="text" name="finding" value="" placeholder="キーワードを入力してください">
        <input class="header-finding-submit" type="submit" name="" value="検索">
      </form>

    </div>

    <div class="header-right">
      <div class="header-btn-container">

        <?php
        if(empty($_SESSION['user_id'])){
          ?>
        <div class="header-btn header-login">
          <a href="login.php">ログイン</a>
        </div>
        <div class="header-btn header-resister">
          <a href="resister.php">新規会員登録</a>
        </div>
      <?php } else{ ?>
         <div class="header-btn header-login">
           <a href="mypage.php">マイページ</a>
         </div>
         <div class="header-btn header-resister">
           <a href="logout.php">ログアウト</a>
         </div>
      <?php } ?>
      </div>
    </div>

  </div>
</header>
