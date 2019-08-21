<?php

//まずはJsonが取得できるか試す　その後整形する
// 指定ユーザーのタイムラインから、指定ワードがあるものを5件抽出する

//token.phpで取得したベアラートークン
$bearerToken = 'AAAAAAAAAAAAAAAAAAAAAOMX%2FgAAAAAAmEq%2BIldSMnN9oRRtoJGes3%2BRMpU%3D4UULIYUd4S8hcPFCw5RpbETMwb2yBiM34f47F0sLPrbWyuFDzy';

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
			'Authorization: Bearer '. $bearerToken,
		) ,
	) ,
) ;

//ストリームコンテキストを作成　file_get_contentsのオプションとして使う時に必要　詳しくは知らん
$Scontext = stream_context_create($context);
$json = file_get_contents($requestUrl, false, $Scontext);
var_dump($json);
echo $json;


?>