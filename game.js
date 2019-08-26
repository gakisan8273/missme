$(function(){

var topValue = 1;

// あそぶ！がクリックされたらタイマー開始
$(".game_start").click(function(){
  $(".start_page").hide();
  $(".game_page").show();

  var count = 0;
  var countUp = function(){
    count++;
    $(".stop_watch").val(count);
  }
  var id = setInterval(function(){
    countUp();
    console.log(count);
  } ,1000);

});


// パネルをクリックした時、見出しと合っていれば成功クラスを付与、違っていれば失敗クラスを付与
// 成功した時、見出しをインクリメントする

//パネルがくりっくされたら
  $(".panel").click(function(){
    // パネルの値を取得
    var panelValue = $(this).text();
    // 見出しの値を取得
    var topValue = $(".top_value").text();
    // falseクラスを全て外す
    $(".false").removeClass("false");

    if (panelValue == 1){


    }

    // パネルと見出しが同じなら
    if(panelValue === topValue){
      // successクラスを付与　cssで非表示にする　歯抜けにするためには？
      $(this).addClass("success");
      // $(this).closest(".panel").fadeOut(); <- 消えるが隙間がつまる

      // 見出しの数字をインクリメント
      topValue++;
      // パネルの最後の数と今の数を判定
      if(topValue <= panelQuantity){
        // 最後まで達していなければ、見出しをインクリメント
        $(".top_value").text(topValue);
      }else{
        // 最後の数字だったら　クリア表示を出す
        $(".panel").hide();
        $(".panel_container").hide();
        $(".img_end").show();
        $(".top h1").text('クリア！おめでとう！！');
        $(".top_value").text('');
        $(".top h1").addClass("blink");
        clearInterval(1);

      }

      // パネルと見出しが違ってたら
    }else{
      // falseクラスを付与
      $(this).addClass("false");
    }

  });

});
