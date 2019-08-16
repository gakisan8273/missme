window.addEventListener('load', function(){

	console.log('読み込み完了');

	//右側に３段で表示している画像をクリックすると、左側に大きく表示させる
	// クリックした画像のURLを取得
	// 左側の画像のURLをクリックした画像のURLで上書き
	// 左側の画像はクラス名で判別する
	var subImg = document.getElementsByClassName('js-switch-img-sub');
	var mainImg = document.getElementsByClassName('js-switch-img-main');

	console.log(subImg);

	// getElementBtClassNameは配列で返すので、添字を指定する必要がある
	for( var i = 0; i < subImg.length; i++){
		var changeShowImgUrl = subImg[i].addEventListener('click',function(){
			var subImgUrl = this.getAttribute('src');
			console.log(subImgUrl);
			mainImg[0].setAttribute('src',subImgUrl);
			console.log('変更しました');
		},false);
	}
	
	// //お気に入り機能
	// var consider = document.getElementsByClassName('js-click-consider') || null;
	// for( var i=0; i < consider.length; i++){
	// 	var considerDrawingId[i] = consider[i].data('drawingId') || null;
	// }

	// //図面IDがあれば（０も含む） 否定は、図面IDがundefined or null
	// if(considerDrawingId === undefined || considerDrawingId === null){
	// 	// 何もしない
	// }else{
	// 	//図面IDがある場合
	// 	consider
	// }



}, false);