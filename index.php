<?php
$ret=array();

if(!empty($_GET)){
  $ch=curl_init();

  curl_setopt($ch,CURLOPT_URL,$_GET["uri"]);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
  curl_setopt($ch,CURLOPT_HEADER,true);

  if(!empty($_GET["headers"])){
    $headers=urldecode($_GET["headers"]);
    $headers_composed=[];
    
    foreach(array_keys($headers) as $kiy){array_push($headers_composed,"$kiy: "+$headers[$kiy]);}

    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
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

header("Content-type: application/json");
echo json_encode($ret);
?>
