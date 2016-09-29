<?php
$ret=array();
$sht=false;

if(!empty($_GET)){
  $ch=curl_init();

  curl_setopt($ch,CURLOPT_URL,$_GET["uri"]);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
  curl_setopt($ch,CURLOPT_HEADER,true);

  if(!empty($_GET["headers"])){
    $headers=urldecode($_GET["headers"]);
    $headers_composed=[];
    


    $headers=json_decode($headers,true);
 //    $headers["cookie"]=urldecode($_GET["cookie"]);
    $headers["Cookie"]="csrftoken=6EsbogXqgHuCJ8fOBeV1uR3Hp0WEmo4P; ds_user=aingcreations; ds_user_id=1172595034; igfl=aingcreations; is_starred_enabled=yes; mid=V-taxQABAAGW-Ia_BfaWkftgY9qq; sessionid=IGSC866e55e13afc991436d89362e2ff5d32a14711b844f25bf65d44134d0a500187%3Al4KD9BSWvWt36vXmQAqM63hd8K3St8T7%3A%7B%22_token_ver%22%3A2%2C%22_auth_user_id%22%3A1172595034%2C%22_token%22%3A%221172595034%3Ar9Zt6ccc1Lh93Ut4Kb3QflSVuggXXhbh%3A6a8d6cc3c7bafbd992c6ca12f17ab8a4f3ed96ae641e731a70d3d3f123fcd2f2%22%2C%22asns%22%3A%7B%22118.97.114.254%22%3A17974%2C%22time%22%3A1475041991%7D%2C%22_auth_user_backend%22%3A%22accounts.backends.CaseInsensitiveModelBackend%22%2C%22last_refreshed%22%3A1475041991.949082%2C%22_platform%22%3A1%2C%22_auth_user_hash%22%3A%22%22%7D";

    foreach($headers as $key => $value)
{
  array_push($headers_composed,$key.": ".$value);
}
  //  foreach(key($headers) as $kiy){echo "$kiy: "+$headers[$kiy];array_push($headers_composed,"$kiy: "+$headers[$kiy]);}


    $sht=$headers_composed;

    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers_composed);
  }

  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
  curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
  curl_setopt($ch,CURLOPT_VERBOSE,false);
  curl_setopt($ch,CURLINFO_HEADER_OUT,true);

  if(!empty($_GET["method"])&&$_GET["method"]=="POST"&&!empty($_POST)){
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$_POST);
  }

  $resp=curl_exec($ch);
  $header_len=curl_getinfo($ch,CURLINFO_HEADER_SIZE);

  $headers=substr($resp,0,$header_len);
  $body=substr($resp,$header_len);


  $headers_composed=array();
  $headers_text=substr($headers,0,strpos($headers,"\r\n\r\n"));
  foreach(explode("\r\n",$headers_text) as $i=>$line){
    if($i==0){$headers_composed["http_code"]=$line;}else{
      $headers_composed[explode(": ",$line)[0]]=explode(": ",$line)[1];
    }
  }

  curl_close($ch);
  $ret=array("headers"=>$headers_composed,"body"=>json_decode($body));
}else{$ret=array("error"=>array("message"=>"Unknown parameter!"));}

function getRequestHeaders() {
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}

$ret["req_headers"]= getRequestHeaders();
$ret["sent_headers"]=$sht;

header("Content-type: application/json");
echo json_encode($ret);
?>
