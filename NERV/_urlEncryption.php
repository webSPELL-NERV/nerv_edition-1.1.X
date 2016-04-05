<?php
	
	function decryptStringArray ($stringArray, $key = _URLENCRYPTION_PASSWORD)
	{
		$s = unserialize(rtrim(mcrypt_decrypt(_URLENCRYPTION_LEVEL, md5($key), base64_decode(strtr($stringArray, '-_,', '+/=')), MCRYPT_MODE_CBC, md5(md5($key))), "\0"));
		return $s;
	}

	function encryptStringArray ($stringArray, $key = _URLENCRYPTION_PASSWORD) 
	{
		$s = strtr(base64_encode(mcrypt_encrypt(_URLENCRYPTION_LEVEL, md5($key), serialize($stringArray), MCRYPT_MODE_CBC, md5(md5($key)))), '+/=', '-_,');
		return $s;
	}

	function prepareUrl($url, $key = _URLENCRYPTION_PASSWORD)
	{
		if(_URLENCRYPTION_ENABLED){
			$url = explode("?",$url,2);
			if(sizeof($url) <= 1){
				return $url[0];
			}else{
				$encryptedURL = encryptStringArray($url[1],$key);
				$site = getSITE($url[1]);
				if($site && _URLENCRYPTION_SHOWSITE){
					$url2 = $url[0].$site."&_secret=".$encryptedURL;
				}else{
					$url2 = $url[0]."?_secret=".$encryptedURL;
				}
				//$url2 = $site."/".$encryptedURL."/";
				return $url2;
			}
		}else{return $url;}
	}

	function getSITE($eURL){
		$param_pairs = explode('&',$eURL);
		$split_pair = explode('=',$param_pairs[0]);
		if($split_pair[0]=="site"){
			//return $split_pair[1];
			return "?site=".$split_pair[1];
		}
		return "";
	}
	
	function setGET($params,$key = _URLENCRYPTION_PASSWORD) 
	{
		unset($_GET);
		$params = decryptStringArray($params,$key);
		$param_pairs = explode('&',$params);
		foreach($param_pairs as $pair)
		{
			$split_pair = explode('=',$pair);
			$_GET[$split_pair[0]] = $split_pair[1];
			$_REQUEST[$split_pair[0]] = $split_pair[1];
			// if($split_pair[1]=="true" || $split_pair[1]=="false"){
				// if($split_pair[1]=="true"){
					// $_REQUEST[$split_pair[0]] = true;
				// }else{
					// $_REQUEST[$split_pair[0]] = false;
				// }
			// }
		}
	}
?>