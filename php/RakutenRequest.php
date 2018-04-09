<?php
//======================================================================
// name    : RakutenRequest
// remarks : RakutenAPIﾘｸｴｽﾄｸﾗｽ
//======================================================================
class RakutenRequest {

	//-----------------------------------------------------
	// ﾒﾝﾊﾞ変数
	//-----------------------------------------------------
		// URL
		protected $Baseurl;
		// ｱｸｾｽｷｰ
		protected $access_key_id;
		// ｼｰｸﾚｯﾄｷｰ
		protected $secret_access_key;
		// ｱｿｼｴｲﾄﾀｸﾞ
		protected $associate_tag;

	//-----------------------------------------------------
	// ｺﾝｽﾄﾗｸﾀ
	//-----------------------------------------------------
		//-------------------------------------------------
		// name  : __construct
		// param : $config ｱｸｾｽｷｰ
		//-------------------------------------------------
		public function __construct($config){
			$this->setAccessKeyId($config['access_key_id']);
			$this->setSecretAccessKey($config['secret_access_key']);
			$this->setAssociateTag($config['associate_tag']);
			$this->Baseurl = $config['base_url'];
		}

	//-----------------------------------------------------
	// ｹﾞｯﾀｰ ｾｯﾀｰ
	//-----------------------------------------------------
		//-------------------------------------------------
		// ｱｸｾｽｷｰｾｯﾀｰ
		//-------------------------------------------------
		public function setAccessKeyId($access_key_id) {
			$this->access_key_id = $access_key_id;
			return $this;
		}
		//-------------------------------------------------
		// ｱｸｾｽｷｰｹﾞｯﾀｰ
		//-------------------------------------------------
		public function getAccessKeyId() {
			return $this->access_key_id;
		}
		//-------------------------------------------------
		// ｼｰｸﾚｯﾄｱｸｾｽｷｰｾｯﾀｰ
		//-------------------------------------------------
		public function setSecretAccessKey($secret_access_key) {
			$this->secret_access_key = $secret_access_key;
			return $this;
		}
		//-------------------------------------------------
		// ｼｰｸﾚｯﾄｱｸｾｽｷｰｹﾞｯﾀｰ
		//-------------------------------------------------
		public function getSecretAccessKey() {
			return $this->secret_access_key;
		}
		//-------------------------------------------------
		// ｱｿｼｴｲﾄﾀｸﾞｾｯﾀｰ
		//-------------------------------------------------
		public function setAssociateTag($associate_tag) {
			$this->associate_tag = $associate_tag;
			return $this;
		}
		//-------------------------------------------------
		// ｱｿｼｴｲﾄﾀｸﾞｹﾞｯﾀｰ
		//-------------------------------------------------
		public function getAssociateTag() {
			return $this->associate_tag;
		}
		//-------------------------------------------------
		// Timestampｹﾞｯﾀｰ
		//-------------------------------------------------
		public  function getTimeStamp() {
			return gmdate('Y-m-d\TH:i:s\Z');
		}

	//-----------------------------------------------------
	// ﾒｿｯﾄﾞ
	//-----------------------------------------------------
		//-------------------------------------------------
		// name   : ItemSearch
		// param  : $params
		// remark : 商品検索のﾊﾟﾗﾒｰﾀ設定
		//-------------------------------------------------
		public  function ItemSearch($format,$keyword){
			$params = array();
			$params['format'] = $format;
			$params['keyword'] = $this->urlencode_rfc3986($keyword);
			$params['applicationId'] = $this->getAccessKeyId();
			return $this->setUrl($params);
		}
		//-------------------------------------------------
		// name   : setUrl
		// param  : $params
		// remark : ﾊﾟﾗﾒｰﾀをﾘｸｴｽﾄ用のURLに変換
		//-------------------------------------------------
		protected  function setUrl($params = array()){

			//ksort($params);
			//  canonical stringの作成
			$canonical_string = '';
			foreach($params as $k => $v) {
				$canonical_string .= '&' . $k . '=' . $v;
			}
			$canonical_string = substr($canonical_string, 1);
			// ﾘｸｴｽﾄURL
			$url = $this->Baseurl.'?'.$canonical_string;

			return $url;
		}
		//-------------------------------------------------
		// name   : request
		// param  : $url
		// remark : APIﾘｸｴｽﾄ
		//-------------------------------------------------
		public  function request($url){
			 $ch = curl_init($url);
		     curl_setopt($ch, CURLOPT_HEADER, FALSE);
		     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		     $result = curl_exec($ch);
		     curl_close($ch);

		     return $result;
		}
		//-------------------------------------------------
		// name   : urlencode_rfc3986
		// param  : $url
		// remark : RFC3986形式でURLｴﾝｺｰﾄﾞ
		//-------------------------------------------------
		public function urlencode_rfc3986($str) {
			return str_replace('%7E', '~', rawurlencode($str));
		}
}