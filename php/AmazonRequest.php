<?php
//======================================================================
// name    : Request
// remarks : AmazonAPIﾘｸｴｽﾄｸﾗｽ
//======================================================================
class Request {

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
		// ｻｰﾋﾞｽ
		protected $Service;
		// APIﾊﾞｰｼﾞｮﾝ
		public $Version;

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
			$this->Service = $config['service'];
			$this->Version = $config['version'];
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
		public  function ItemSearch($params = array()){
			return $this->getUrl(
					array_merge(
							array('Operation'=>'ItemSearch'),$params));
		}
		//-------------------------------------------------
		// name   : getUrl
		// param  : $params
		// remark : ﾘｸｴｽﾄ用のURLを取得
		//-------------------------------------------------
		public function getUrl($params = array()){
			return  $this->setUrl(
					array_merge(array(
							'Service' => $this->Service,
							'Version' => $this->Version,
							'AWSAccessKeyId' => $this->getAccessKeyId(),
							'Timestamp' => $this->getTimeStamp(),
							'AssociateTag' => $this->getAssociateTag()
					),$params));
		}
		//-------------------------------------------------
		// name   : setUrl
		// param  : $params
		// remark : ﾊﾟﾗﾒｰﾀをﾘｸｴｽﾄ用のURLに変換
		//-------------------------------------------------
		protected  function setUrl($params = array()){

			ksort($params);
			//  canonical stringの作成
			$canonical_string = '';
			foreach ($params as $k => $v) {
				$canonical_string .= '&'.$this->urlencode_rfc3986($k).'='.$this->urlencode_rfc3986($v);
			}
			$canonical_string = substr($canonical_string, 1);

			$parsed_url = parse_url($this->Baseurl);
			// HMAC-SHA256 を計算
			$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$canonical_string}";
			// BASE64 エンコード
			$signature = base64_encode(hash_hmac('sha256', $string_to_sign,  $this->getSecretAccessKey(), true));
			// リクエストURL作成、末尾に署名を追加
			$url = $this->Baseurl.'?'.$canonical_string.'&Signature='.$this->urlencode_rfc3986($signature);


			return $url;
		}
		//-------------------------------------------------
		// name   : request
		// param  : $url
		// remark : APIﾘｸｴｽﾄ
		//-------------------------------------------------
		public  function request($url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			$response = curl_exec($ch);
			curl_close($ch);

			return simplexml_load_string($response);
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