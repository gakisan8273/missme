<footer id="footer">
  <div class="site-width">
    <div class="footer-top">
      <div class="logo-container">
        <!-- <a href="" class="logo logo-small logo-small1">ものづくりの、明日を支える。</a> -->
        <div class="logo-set">
          <img class="logo-img" src="img/logo.png" alt="">
          <a href="index.php" class="logo logo-big">MissMe-GAKI</a>
        </div>
        <!-- <a href="" class="logo logo-small logo-small2">生産材コマース</a> -->
      </div>
      <p class="footer-top-sentence">Sales area</p>
    </div>
    <div class="footer-country">
      <ul>
        <li>中国</li>
        <li>韓国</li>
        <li>台湾</li>
        <li>ベトナム</li>
        <li>タイ</li>
        <li>シンガポール</li>
        <li>マレーシア</li>
        <li>インドネシア</li>
        <li>インド</li>
        <li>ドイツ</li>
        <li>イタリア</li>
        <li>フランス</li>
        <li>イギリス</li>
        <li>アメリカ</li>
        <li>メキシコ</li>
      </ul>
    </div>
    <p style="font-size:14px; margin-bottom:30px;">Copyright MissMe Corporation All Rights Reserved.</p>
  </div>

</footer>

<script type="text/javascript" src="jquery-3.4.1.min.js"></script>
<script src="drawingDetail.js"></script>
<script src="considerDrawing.js"></script>

<script>
//フッターを最下部に固定
$(function(){

  console.log('フッター高さ変更開始');
  $ftr = $('#footer');
  //window高さを取得し、フッターの開始位置と比較する
  // ウィンドウ高さの方が高い→画面中央よりにフッターが表示されている　のであれば、フッター開始位置を最下部＋フッター高さにする
  var windowHeight = window.innerHeight;
  var ftrHeight = $ftr.innerHeight();
  var ftrPosition = $ftr.offset().top;
  console.log(windowHeight);
  console.log(ftrHeight);
  console.log(ftrPosition);
  if(ftrPosition + ftrHeight < windowHeight){
    $ftr.attr({'style': 'position:fixed; top:' + (windowHeight - ftrHeight) + 'px'});
    console.log('フッター高さ変更');
  }else{
    console.log('フッター高さ変更なし');
  }

//画像ライブプレビュー
var $dropArea = $('.area-drop');
var $fileInput = $('.input-file');
 //ドラッグしているの処理
$dropArea.on('dragover', function(e){
  // e.stopPropagation(); //親要素へのイベントの伝播を止める
  // e.preventDefault(); //デフォルトの動作を止める
  $(this).addClass('hasImg');
  console.log('hasImg追加');
});
 //ドロップしたときの処理
 $dropArea.on('dragleave', function(e){
  // e.stopPropagation(); //親要素へのイベントの伝播を止める
  // e.preventDefault(); //デフォルトの動作を止める
  $(this).removeClass('hasImg');
  console.log('hasImg解除');
});

// ファイルを指定したときの処理
$fileInput.on('change', function(e){
  console.log(e);
  
  var $dropArea = $(this).closest('.area-drop');
  console.log($dropArea);
  $dropArea.removeClass('hasImg');
  console.log('hasImg解除');
  var inputFile = this.files[0];
  console.log(inputFile);
  var $img = $(this).siblings('.prev-img');//兄弟のprev-imgクラスを取得
  console.log($img);
  var fileReader = new FileReader(); //ファイルを読み込むオブジェクト　非同期

  //画像読み込み　これがないと画像設定されない　srcも・・・なぜ？
  fileReader.readAsDataURL(inputFile);


  fileReader.onload = function(event){
    //読み込んだデータをimgに設定
    console.log('src更新');
    $img.attr('src', event.target.result);
    console.log(event.target.result);
  };

});


//データを送信したときにモーダルウィンドウで成功を表示する

// 送信成功するとTOPページに遷移する　その時にセッションにメッセージを仕込む
// あと成功フラグも
// メッセージを取得したらそのセッションのメッセージは空にする　成功フラグも
// そうしないとリロードした時に送信成功と判定されてしまう
// 成功フラグがあれば、モーダルウィンドウをshowする ゆっくりね
// 〜〜が成功しました　的なことを表示する
// 閉じるボタンを押せばcloseする
var $overlay =  $('#overlay');
var $modalWindow = $('#modalWindow');

if($overlay.hasClass('s_flg')){
    //モーダルウィンドウの位置を中央に揃える
  //ウィンドウサイズを取得
  var windowWidth = window.innerWidth;
  // var modalWidth = $('#modalWindow').css('width').slice(0,-2);
  var modalWidth = $('#modalWindow').outerWidth();
  console.log('ウィンドウ幅');
  console.log(windowWidth);
  console.log('モーダルウィンドウ幅');
  console.log(modalWidth);
  console.log('モーダルウィンドウのleft初期値');
  console.log($('#modalWindow').css('left'));
  $('#modalWindow').css({'left' : ((windowWidth - modalWidth) / 2 ) + 'px'}) ;
  $overlay.show();
  $modalWindow.show('slow');
}

$overlay.on('click',function(){
  console.log('オーバーレイがクリックされました');
  $modalWindow.hide();
  $overlay.hide();
});
$modalWindow.on('click',function(){
  console.log('モーダルウィンドウがクリックされました');
  $modalWindow.hide();
  $overlay.hide();
});

});

</script>