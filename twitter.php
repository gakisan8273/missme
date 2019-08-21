<?php
// 設定項目
$api_key = "Zl0VLdCBZI9kpUBlDxoD45f4v" ;	// APIキー
$api_secret = "1cLPMbh44FPEmmg44iGLl89gOIOnFtIQUelSrys3cS14QeWKNW" ;	// APIシークレット

// クレデンシャルを作成
$credential = base64_encode( $api_key . ":" . $api_secret ) ;

// リクエストURL
$request_url = "https://api.twitter.com/oauth2/token" ;

// リクエスト用のコンテキストを作成する
$context = array(
	"http" => array(
		"method" => "POST" , // リクエストメソッド
		"header" => array(			  // ヘッダー
			"Authorization: Basic " . $credential ,
			"Content-Type: application/x-www-form-urlencoded;charset=UTF-8" ,
		) ,
		"content" => http_build_query(	// ボディ
			array(
				"grant_type" => "client_credentials" ,
			)
		) ,
	) ,
) ;

// cURLを使ってリクエスト
$curl = curl_init() ;
curl_setopt( $curl, CURLOPT_URL , $request_url ) ;	// リクエストURL
curl_setopt( $curl, CURLOPT_HEADER, true ) ;	// ヘッダーを取得する 
curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context["http"]["method"] ) ;	// メソッド
curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// 証明書の検証を行わない
curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_execの結果を文字列で返す
curl_setopt( $curl, CURLOPT_HTTPHEADER , $context["http"]["header"] ) ;	// ヘッダー
curl_setopt( $curl, CURLOPT_POSTFIELDS , $context["http"]["content"] ) ;	// リクエストボディ
curl_setopt( $curl, CURLOPT_TIMEOUT , 5 ) ;	// タイムアウトの秒数
$res1 = curl_exec( $curl ) ;
$res2 = curl_getinfo( $curl ) ;
curl_close( $curl ) ;

// 取得したデータ
$response = substr( $res1, $res2["header_size"] ) ;	// 取得したデータ(JSONなど)
$header = substr( $res1, 0, $res2["header_size"] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)

// [cURL]ではなく、[file_get_contents()]を使うには下記の通りです…
// $response = file_get_contents( $request_url , false , stream_context_create( $context ) ) ;

// JSONを配列に変換する
$arr = json_decode( $response, true ) ;

// 配列の内容を出力する (本番では不要)
echo '<p>下記の認証情報を取得しました。(<a href="' . explode( "?", $_SERVER["REQUEST_URI"] )[0] . '">もう1回やってみる</a>)</p>' ;

foreach ( $arr as $key => $value ) {
	echo "<b>" . $key . "</b>: " . $value . "<BR>" ;
}



//まずはJsonが取得できるか試す　その後整形する
// 指定ユーザーのタイムラインから、指定ワードがあるものを5件抽出する

//token.phpで取得したベアラートークン
$bearerToken = $value;

//ユーザーのタイムライン
// https://developer.twitter.com/en/docs/tweets/timelines/api-reference/get-statuses-user_timeline　から
$requestUrl ='https://api.twitter.com/1.1/statuses/user_timeline.json';

//リクエストURLにパラメータを追加
$params = array(
    'user_id' => '@gakisan8273', //まずは自分のをベタうちしてテスト
    'count'   => 5, //5件取得する　多すぎてもうざいので
);

if($params){
    $Qparams = http_build_query($params);
    $requestUrl .= '?'.$Qparams;
}

// リクエスト用のコンテキスト
$context = array(
	'http' => array(
		'method' => 'GET' , // リクエストメソッド
		'header' => array(			  // ヘッダー
			'Authorization: Bearer '. $value,
		) ,
	) ,
) ;

//ストリームコンテキストを作成　file_get_contentsのオプションとして使う時に必要　詳しくは知らん
$Scontext = stream_context_create($context);
$json = file_get_contents($requestUrl, false, $Scontext);

echo $json;

?>