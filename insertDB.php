<?php

require('function.php');


// for($i=3;$i<100;$i++){
// // ユーザーを１００に増やす
// 	$dbh = dbconnect();
// 	$sql = 'INSERT INTO users (name, address, tel, company, email, password, login_time, create_date) 
// 	value(:name, "S市杜王町", "00000000000", "海馬コーポレーション1次請", :email, :password, :login_time, :create_date)';
// 	$data = array(
// 			":name" => 'ユーザー'.$i,
// 			":email" => 'test'.$i.'@test',
// 			":password" => password_hash('111111',PASSWORD_DEFAULT),
// 			":login_time" => date('Y-m-d h:i:s'),
// 			":create_date" => date('Y-m-d h:i:s'),
// 	);
// 	$stmt = queryPost($dbh,$sql,$data);

// }

//１ユーザーあたり5件の依頼を登録する

for($u_id=1 ; $u_id < 100; $u_id++){
	for($i=1 ; $i <= 5 ; $i++){


		$dbh = dbconnect();
		$sql = 'INSERT INTO drawings (title, drawing_no, category_id, detail, pic1, pic2, pic3, submit_user_id, submit_price,estimate_due_date,work_due_date, create_date) 
		value(:title, :d_no, :c_id, "サンプルテキスト", :pic1, :pic2, :pic3,:u_id,:price,:e_dd,:w_dd, :create_date)';
		$data = array(
				":title" => 'サンプル図面'.$i,
				":d_no" => sprintf('04d', $u_id).'-'.sprintf('04d', $i) ,
				":c_id" => $i,
				":pic1" => 'uploads/'.$i.'.jpeg',
				":pic2" => 'uploads/pic2.png',
				":pic3" => 'uploads/pic3.png',
				":u_id" => $u_id,
				":price" => mt_rand(1,100)*1000, //1,000~100,000
				":e_dd" =>  date('Y-m-d', time()+mt_rand(60*60*24*1,60*60*24*90) ),
				":w_dd" => date('Y-m-d', time()+mt_rand(60*60*24*1,60*60*24*90)+60*60*24*1 ),
				":create_date" => date('Y-m-d h:i:s'),
		);
		$stmt = queryPost($dbh,$sql,$data);
	}
}

// 検討中　受注　見積もり　は手動で行う





?>