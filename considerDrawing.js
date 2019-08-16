$(function(){

	console.log('読み込み完了（jQuery)');

//お気に入り機能
var consider = $('.js-click-consider') || null;
var considerDrawingId = consider.data('drawingid') || null;

console.log(consider);
console.log(considerDrawingId);

//図面IDがあれば（０も含む） 否定は、図面IDがundefined or null
if(considerDrawingId !== undefined && considerDrawingId !== null){
    //図面IDがある場合
    consider.on('click',function(){
			// var $this = $(this); //thisだとクリックしたもののみになる
			$.ajax({
				type:"POST",
				url:"ajaxConsider.php",
				data: {drawingId : considerDrawingId},
			}).done(function( data ){ 
				console.log('Ajax Success');
				// console.log($(this));
				consider.toggleClass("consider-active");
				//consider-activeがついているかどうかで表示テキストを変える
				console.log(consider.text());
				if(consider.hasClass("consider-active")){
					consider.text('"検討中"に追加済み');
					console.log('"検討中"に追加済み　に変えました');
				}else{
					consider.text('"検討中"に追加する');
					console.log('"検討中"に追加する　に変えました');
				}

			}).fail(function( msg ) {
				console.log('Ajax Error');
			});
		});
	}
});

