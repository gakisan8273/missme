<?php
//共通変数・関数ファイルを読み込み
  require('function.php');
  debug('-------------------------------------------------');
  debug('');
  debug('　トップページ　');
  debug('');
  debug('-------------------------------------------------');
  debugLogStart();

  $pageTitle = 'MissMe top';
 ?>

<?php
setlocale(LC_MONETARY, 'ja_JP.UTF-8');//通貨表示用
var_dump(setlocale(LC_MONETARY, 'ja_JP.UTF-8'));
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


//画面表示用データ取得
// GETパラメータを取得
debug('"GETパラメータ : '.print_r($_GET,true));

// 現在のページ数
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは１ページ
$currentPageNum = (int)$currentPageNum;
// var_dump($currentPageNum);
debug("現在ページ数：".print_r($currentPageNum,true));

//パラメータに不正な値が入っていないかチェック
if($currentPageNum === 0 ){ //URLパラメータのpが整数でなければ（手入力で何かを入れた場合） 文字列をint変換すると0になる
  error_log('エラー発生：指定ページに不正な値が入りました');
  debug('トップページに遷移します');
  header("Location:index.php");
  exit();
}


// カテゴリー
$category = (!empty($_GET['category'])) ? $_GET['category'] : ''; //URLパラメータにカテゴリIDなければ空
debug("抽出カテゴリ：".print_r($category,true));
// ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
debug("ソート方法：".print_r($sort,true));


//表示件数　３０or15or45 GETで変えられるようにする
$listSpan = (!empty($_GET['show'])) ? $_GET['show'] : 30;
debug("表示件数：".print_r($listSpan,true));

//検索窓からキーワード検索をする
// GET送信し、キーワードがURLパラメータに格納される
// GET['finding']がdrawingsのtitleと部分一致するものを表示する
$keyWord = (!empty($_GET['finding']))? $_GET['finding']:'';
debug('キーワード：'.print_r($keyWord,true));


//現在の表示レコード先頭を算出　初めはゼロ？
$currentMinNun =(($currentPageNum-1)*$listSpan); //(1-1)*45 = 0 (2-1)*45=45
debug("表示レコード先頭：".print_r($currentMinNun,true));
//DBから図面データを取得
$dbDrawingData = getDrawingList($currentMinNun, $category, $sort, $listSpan, $keyWord);
// debug('getDrawingList返り値：'.print_r($dbDrawingData,true));

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

<!-- ここからsidebar.php index.phpに書いてあるものが最新 -->
<?php $addGet['category'] = appendGetParam(array('category','p','finding'));?>

 <aside class="sidebar">
  <div class="sidebar-category sidebar-select">
    <p class="select-top">カテゴリから探す</p>
    <ul>
      <li class="category-items"><a class="category-sentence" href="index.php?category=1<?php echo $addGet['category'];?>">旋盤<i class="fas fa-cut"></i></a></li>
      <li class="category-items"><a class="category-sentence" href="index.php?category=2<?php echo $addGet['category'];?>">フライス盤<i class="fas fa-highlighter"></i></a></li>
      <li class="category-items"><a class="category-sentence" href="index.php?category=3<?php echo $addGet['category'];?>">マシニング<i class="fas fa-crosshairs"></i></a></li>
      <li class="category-items"><a class="category-sentence" href="index.php?category=4<?php echo $addGet['category'];?>">曲げ<i class="fas fa-bezier-curve"></i></a></li>
      <li class="category-items"><a class="category-sentence" href="index.php?category=5<?php echo $addGet['category'];?>">表面処理<i class="fas fa-paint-brush"></i></a></li>
    </ul>
  </div>

  <div class="sideber-place sidebar-select">
    <p class="select-top">発注元住所から探す</p>
    <ul>
      <li class="category-items">伊勢崎市<i class="fas fa-pencil-alt"></i></li>
      <li class="category-items">高崎市<i class="fas fa-pen-fancy"></i></li>
      <li class="category-items">前橋市<i class="fas fa-ruler-horizontal"></i></li>
      <li class="category-items">太田市<i class="fas fa-stamp"></i></li>
      <li class="category-items">館林市<i class="fas fa-ruler-combined"></i></li>
    </ul>
  </div>
</aside>
<!-- ここまでsidebar.php -->

      <main class="main-contents">
        <div class="main-header">
          <div class="main-top-left">
            <p>
              <span>
                <?php
                  echo (!empty($dbDrawingData['data'])) ? $currentMinNun + 1 : 0; //検索結果がなければ0 currentMinNum（先頭レコード）は０から始まっている
                ?>
              </span> 
               - 
              <span>
                <?php echo $currentMinNun + count($dbDrawingData['data']); //先頭レコードから表示件数を足した値?>
              </span>
               / 
              <span class="main-top-big">
              <?php
                echo $dbDrawingData['total'];
              ?>
              </span>件中
            </p>
          </div>
          <div class="main-top-right">

          <?php $addGet['show'] = appendGetParam(array('show'));?>

          <p class="show-items main-top-sentence">表示件数</p>
            <p class="show-items kennsuu <?php echo ((int)$_GET['show'] === 15)?  'show-items-active' : '';?>"><a href="index.php?show=15<?php echo $addGet['show'];?>">15件</a></p>
            <p class="show-items kennsuu <?php echo ((int)$_GET['show'] === 30 or empty($_GET['show']))?  'show-items-active' : '';?>"><a href="index.php?show=30<?php echo $addGet['show'];?>">30件</a></p>
            <p class="show-items kennsuu <?php echo ((int)$_GET['show'] === 45)?  'show-items-active' : '';?>"><a href="index.php?show=45<?php echo $addGet['show'];?>">45件</a></p>


            <p class="show-items main-top-sentence">並び替え</p>
            <form action="" method="get">
            <select class="show-items order" name="sort">
              <option value="0">選択してください</option>
              <option value="1">納期 昇順</option>
              <option value="2">納期 降順</option>
              <option value="3">提示金額 昇順</option>
              <option value="4">提示金額 降順</option>
            </select>
            <?php if(!empty($_GET['show'])){?>
              <input type="hidden" name="show" value=<?php echo $_GET['show'];?>>
            <?php }?>
            <?php if(!empty($_GET['category'])){?>
              <input type="hidden" name="category" value=<?php echo $_GET['category'];?>>
            <?php }?>
            <?php if(!empty($_GET['finding'])){?>
              <input type="hidden" name="finding" value=<?php echo $_GET['finding'];?>>
            <?php }?>
            <input class="sort-submit" type="submit" value="実行">
            </form>
          </div>
        </div>

        <div class="main-page">


        <!-- //ページング -->
        <?php 
          // var_dump($currentPageNum);
          // var_dump($dbDrawingData['total_page']);
          
          // debug('現在ページ数：'.print_r($currentPageNum,true));
          $minPageNum = pagenation($currentPageNum, $dbDrawingData['total_page'])[0];
          $maxPageNum = pagenation($currentPageNum, $dbDrawingData['total_page'])[1];
          debug('表示する最小ページ数：'.print_r($minPageNum,true));
          debug('表示する最大ページ数：'.print_r($maxPageNum,true));
         ?>

          <?php require('pagenation.php') ?>

        </div>

        
        <div class="main-panel-list">

        <?php foreach($dbDrawingData['data'] as $drawing){?>
          <a class="main-panel" href="drawingDetail.php?d_id=<?php echo $drawing['id']; ?>">
            <div class="panel-head">
                <img class="panel-img" src="<?php echo $drawing['pic1'];?>" alt="">
            </div>
            <div class="panel-body">
              <p class="panel-title"><?php echo $drawing['title'];?></p>
              <p class="panel-price">希望価格 ： <?php echo money_format("%n",$drawing['submit_price']);?></p>
              <p class="panel-duedate">希望納期 ： <?php echo $drawing['work_due_date'];?></p>
            </div>
          </a>
        <?php }?>
        
      </div>
      <?php require('pagenation.php') ?>
      </main>
    </section>

<?php require('footer.php'); ?>

  </body>
</html>
