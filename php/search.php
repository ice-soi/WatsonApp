<?php
//======================================================================
// POSTのﾊﾟﾗﾒｰﾀを元にRakutenから商品検索を行う
//======================================================================

	//-----------------------------------------------------
	// 定数
	//-----------------------------------------------------
		define("Access_Key_ID","accesskeyid");													// Rakuten APIのｱｸｾｽｷｰ
		define("Secret_Access_Key", "accesskeyid");  						// Rakuten APIのｼｰｸﾚｯﾄｷｰ
		define("Associate_tag", "associatetag");                              	// Rakuten APIのｱｿｼｴｲﾄID
		define("Base_url", "https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706");     	// Rakuten APIのURL

	//-----------------------------------------------------
	// ﾘｸｴｽﾄﾊﾟﾗﾒｰﾀ取得
	//-----------------------------------------------------
		$SearchIndex = $_POST['search_index'];	// 検索ｶﾃｺﾞﾘ
		$Keywords = $_POST['keywords'];        // 検索ﾜｰﾄﾞ

	//-----------------------------------------------------
	// ﾘｸｴｽﾄﾃﾞｰﾀの生成
	//-----------------------------------------------------
		// apiﾘｸｴｽﾄｸﾗｽのｲﾝﾎﾟｰﾄ
		require_once 'RakutenRequest.php';

		// apiﾘｸｴｽﾄｸﾗｽの生成
		$api=new RakutenRequest(array(
				'access_key_id' =>Access_Key_ID,
				'secret_access_key' =>Secret_Access_Key,
				'associate_tag' => Associate_tag,
				'base_url' => Base_url
		));

		// ﾘｸｴｽﾄ用のURLを生成
		$url = $api->ItemSearch('xml',$Keywords);

	//-----------------------------------------------------
	// APIﾘｸｴｽﾄ
	//-----------------------------------------------------
		// ﾚｽﾎﾟﾝｽﾍｯﾀﾞｰの設定
		header('Content-type: application/json');
		// 検索結果を取得
		if (($result = $api->request($url)) == FALSE) {
			return FALSE;
		}
		// JSONの返却
		echo json_encode(simplexml_load_string($result));
