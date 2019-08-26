<?php
error_reporting(E_ALL); //全てのエラーを報告する
ini_set('display_errors','On'); //画面にエラーを表示させるか

  $PANELQUANTITY = 16; //パネルの個数
  $panelArray = range(1,$PANELQUANTITY);
  shuffle($panelArray);


// POSTされていれば、開始時間をDBに登録？　終了時間をDBに登録して差分でかかった時間を算出？


// POSTの値（秒数）を取得
// 秒数とタイムスタンプをDBに登録

// $dsn = 'mysql:dbname=Child_game;host=localhost;charset=utf8'; //Data Source Name
// $user = 'root';
// $password = 'root';
// $options = array(
//   // SQL実行失敗時に例外をスロー
//   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//   // デフォルトフェッチモードを連想配列形式に設定
//   PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//   // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
//   // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
//   PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
// );

// $dbh = new PDO($dsn, $user, $password, $options);

// if(!empty($_POST)){
//   $clear_time = $_POST['stop_watch'];
//   $stmt = $dbh->prepare('INSERT INTO clear_time(clearTime) VALUES(:clearTime)');
//   $stmt->execute(array(':clearTime' => $clear_time));
// }


// 過去のタイムスタンプと秒数を、５個まで表示

// $sql = 'SELECT date,clearTime FROM clear_time ORDER BY date DESC LIMIT 5;';
// $stmt = $dbh->query($sql);
// $date = array();
// $time = array();

// while ($result = $stmt->fetch(PDO::FETCH_ASSOC)){

//   $date[] = $result['date'];
//   $time[] = $result['clearTime'];
// }
 ?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>すうじあわせ</title>
    <link rel="stylesheet" type="text/css" href="style_game.css">
  </head>
  <body>
    <header>
      <div class="header_container site-width">
        <div class="btn_container">
          <a class="btn restart" href="game.php">はじめから</a>
          <a class="btn end" href="index.php">やめる</a>
        </div>
      </div>
    </header>

        <section class="top site-width start_page">
            <h1>すうじあわせ</h1>
            <p class="game_start">あそぶ！！</p>
            <!-- <p class="result">まえに あそんだ けっか</p>
            <ul>
              <li><?php echo $date[0].' '.$time[0]. ' びょう<br>' ?></li>
              <li><?php echo $date[1].' '.$time[1]. ' びょう<br>' ?></li>
              <li><?php echo $date[2].' '.$time[2]. ' びょう<br>' ?></li>
              <li><?php echo $date[3].' '.$time[3]. ' びょう<br>' ?></li>
              <li><?php echo $date[4].' '.$time[4]. ' びょう<br>' ?></li>
            </ul> -->
        </section>
    <section class="top site-width game_page">
        <h1>この すうじを　さがしてね！</h1>
        <p class="top_value">1</p> <!--JSで数字をインクリメントしていく-->
    </section>

    <form class="img_end site_width" id="stop_watch" action="game.php" method="post">
        <p>かかったじかん </p>
        <input class="stop_watch" type="text" name="stop_watch" value="">
        <p> びょう</p>
        <input type="submit" name="submit" value="きろく！">
    </form>

    <section class="game site-width game_page">
      <div class="panel_container">
        <!-- パネル生成 -->
      <?php for($i=0; $i < $PANELQUANTITY; $i++){  ?>
        <!-- <div class="panel"> -->
          <p class="panel panel_value color_<?php echo mt_rand(0,4); ?>"><?php echo $panelArray[$i] ?></p>
        <!-- </div> -->
      <?php } ?>

      </div>
      <div class="img_end">
      <img class="site-width img_end" src="images/train_move1.gif"><br><br><br>
        <!-- <?php echo $date[0].' '.$time[0]. ' びょう<br>' ?>
        <?php echo $date[1].' '.$time[1]. ' びょう<br>' ?>
        <?php echo $date[2].' '.$time[2]. ' びょう<br>' ?>
        <?php echo $date[3].' '.$time[3]. ' びょう<br>' ?>
        <?php echo $date[4].' '.$time[4]. ' びょう<br>' ?> -->
      </div>
    </section>

    <!-- JSはbodyの最下段で読み込む -->

    <script type="text/javascript">
      var panelQuantity = '<?php echo $PANELQUANTITY?>';
    </script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="game.js"></script>
  </body>
</html>
