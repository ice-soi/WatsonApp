<?php
//======================================================================
// POSTのﾊﾟﾗﾒｰﾀを元にwatsonから対話内容を取得する
//======================================================================

	//-----------------------------------------------------
	// 定数
	//-----------------------------------------------------
		define("URL", 'url');
		define("USERID", 'userid');
		define("PASSWORD", 'password');

	//-----------------------------------------------------
	// ﾘｸｴｽﾄﾊﾟﾗﾒｰﾀ取得
	//-----------------------------------------------------
		$q = $_POST['question'];			// ﾕｰｻﾞの入力
		$id = $_POST['conversation_id'];	// 対話ID
		$node = $_POST['dialog_node'];		// 対話ﾉｰﾄﾞ

	//-----------------------------------------------------
	// APIﾘｸｴｽﾄﾊﾟﾗﾒｰﾀ作成
	//-----------------------------------------------------
		// 対話内容を設定
		$data = array('input' => array("text" => $q));

		// 子ﾉｰﾄﾞが存在しない場合はidとnodeは設定しない
		if($id != ""){
			$data["context"] = array("conversation_id" => $id,
					"system" => array("dialog_stack" => array(array("dialog_node" => $node)),
					"dialog_turn_counter" => 1,
					"dialog_request_counter" => 1));
		}

	    // watosonﾘｸｴｽﾄ
	  	$curl = curl_init(URL);

	  	// ﾘｸｴｽﾄｵﾌﾟｼｮﾝ
	  	$options = array(
	    		CURLOPT_HTTPHEADER => array(
	      		'Content-Type: application/json',
	    	),
	    	CURLOPT_USERPWD => USERID . ':' . PASSWORD,
	    	CURLOPT_POST => true,
	    	CURLOPT_POSTFIELDS => json_encode($data),
	    	CURLOPT_RETURNTRANSFER => true,
	  	);

  	//-----------------------------------------------------
  	// APIﾘｸｴｽﾄ
  	//-----------------------------------------------------
		curl_setopt_array($curl, $options);
		// ﾚｽﾎﾟﾝｽﾃﾞｰﾀをｼﾘｱﾗｲｽﾞ
		$jsonString = curl_exec($curl);
		$json = json_decode($jsonString, true);
		// ﾍｯﾀﾞｰの設定
		header('Content-type: application/json');
		// ﾚｽﾎﾟﾝｽﾃﾞｰﾀ
		echo json_encode( $json );