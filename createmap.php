<?php
// author : Quydx
// this application use to create mapping file from  a json file
// use in esrally - elasticsearch

	function setValue(&$var){
		$type = gettype($var);
		if ($type == "object"){
			$fields = get_object_vars($var);
			foreach($fields as $key => $value){
				setValue($var->$key);
			}		
		}
		elseif ($type == "array"){
			for($i=0;$i<count($var);++$i){
				$var["$i"] = ["type" => "double"]; 
			}
		}
		else {
			if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/',$var)) {
				$var = ["type" => "ip"];
			}
			elseif (preg_match('/^(?:[1-9]\d{3}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1\d|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[1-9]\d(?:0[48]|[2468][048]|[13579][26])|(?:[2468][048]|[13579][26])00)-02-29)T(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d(?:Z|[+-][01]\d:[0-5]\d)$/',$var)){
				$var = ["type" => "date"];
			}
			elseif (preg_match('/^[\d]+(|\.[\d]+)$/',$var)) {
				$var = ["type" => "double"];
			}
			elseif (preg_match('/true|false/',$var)){
				$var = ["type" => "boolean"];
			}
			elseif (preg_match('/^[0-9]+$/',$var)){
				$var = ["type" => "long"];
			}
			elseif (preg_match('/.{15,}/',$var)){
				$var = ["type" => "keyword"];
			}
			elseif (preg_match('/.{0,15}/',$var)){
				$var = ["type" => "text"];
			}
			
		}
	}
	$json = shell_exec("tail -1 varnish.json");
	$f = json_decode($json);
	$arr = get_object_vars($f);
	foreach($arr as $key => $value){
		setValue($arr["$key"]);
	}
	$jsn = json_encode($arr,JSON_PRETTY_PRINT);
	var_dump($jsn);
	file_put_contents("text.txt", $jsn);	
	$a =[	"type" => [
				"dynamic" => "strict",
				"properties" => $arr
			]
			
		];
	$b = json_encode($a,JSON_PRETTY_PRINT );
	file_put_contents("text1.txt", $b);




?>
