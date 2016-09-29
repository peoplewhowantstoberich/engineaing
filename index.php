<?php
error_reporting(E_ALL);

$ret=array();

if(!empty($_GET)){
  $ch=curl_init();

  curl_setopt($ch,CURLOPT_URL,$_GET["uri"]);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);

  if(!empty($_GET["headers"])){
    $headers=urldecode($_GET["headers"]);
    $headers_composed=[];

    curl_setopt($ch,CURLOPT_HEADER,true);
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
  $headers_composed=array();
  $headers_text=substr($headers,0,strpos($headers,"\r\n\r\n"));
  foreach(explode("\r\n",$headers_text) as $i=>$line){
    if($i==0){$headers_composed["http_code"]=$line;}else{
      list($key,$value)=explode(": ",$line);
      $headers_composed[$key]=$value;
    }
  }

  $body=substr($resp,$header_len);
  
  curl_close($ch);
  $ret=array("headers"=>$headers_composed,"body"=>json_decode($body,true));
}else{$ret=array("error"=>array("message"=>"Unknown parameter!"));}

header("Content-type: application/json");
echo json_encode($ret);
echo json_encode($_GET);
?>
