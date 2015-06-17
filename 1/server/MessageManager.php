<html>
<head>
    <title>XREZ:MessageManager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
	<style>
		body{position:relative;
		margin-left:auto;
		margin-right:auto;
		}
		#wrapper{
		width:auto; border:1px solid #000; min-width:770px; max-width:1024px;
		text-align:left; margin-left:auto; margin-right:auto; position:relative;
		}
		#main {
		float: left;
		width: 70%;
		}
		#leftBar {
		float: left;
		width: 15%;
		}
		#rightBar {
		float: left;
		width: 14%;
		}
		table{
			border:dotted 0px blue;
			/*background-color:white;
			color:#c21e03;*/		
			border-collapse:collapse;
			/*border-spacing:1px;*/
			font-size: 10;
		}
		td{
			border:dotted 0px gray;/**/
			padding: 0px 5px 0px 5px;
		}
		.frameCell{
			border:dotted 1px gray;/**/
		}
	</style>
</head>
<body>
<div id="wrapper" style=" border:dotted 0px grey;">
<div id="leftBar" style="border:dotted 0px brown;"></div>
<div id="main" style="border:dotted 0px grey;">
	<!--LOGO style="border:dotted 0px blue;" -->
	<table border=0 cellPadding=0 cellspacing=0 >
		<tr><td style="border:dotted 0px blue;color:#4D8606;font-family:arial;font-size:20px;text-align:right;">Message</td>
		<td style="color: #f60;font-family:arial;font-size:20px;text-align:left;">Manager</td></tr>
		<tr><td colspan=2 style="color:#494949;font-family:arial;font-size:10px;">&nbsp;沟&nbsp;通&nbsp;·&nbsp;你&nbsp;我</td></tr>	
	</table><br>
	
<?php	
	/*
	Version 1.0 20150616 MessageManager.php
	综合的function process($content)处理数据并返回纯文本，微信模板套用留到server.php的$this->responseText()方法中再执行
	基于微信公众平台 PHP SDK
	参考http://sae.sina.com.cn/?m=apps&a=detail&aid=162
	*/
	
	//全局变量$command，$keyword（在prepare($param);方法中得到正确的值），函数中必须用global标识，否则出错
	$command=0;
	$keyword=null;
	$recordCount=1;
	
	traceHttp();

	$action="";
	$from='TanGuodong'; 
	$to='JiangTing'; 
	$word='';
	$at='';
	$key='';
	if(isset($_GET["action"]))$action=$_GET["action"];
	if(isset($_GET["from"]))	$from=$_GET["from"];
	if(isset($_GET["to"]))	$to=$_GET["to"];
	if(isset($_GET["word"]))	$word=$_GET["word"];
	if(isset($_GET["at"]))	$at=$_GET["at"];
	if(isset($_GET["key"]))	$key=$_GET["key"];
	
	if(strcmp($action , "resetlog")==0){
		resetLog();
		echo "<br>Log was reset.";
	}
	//DEBUG
	if (isset($_GET['request'])) {
		$param = $_GET['request'];
		process($param);
	}
	
	
	



	/*
	process方法处理输入返回最后结果
	*/
	function process($content) 
	{
        //全局变量$command，$keyword（在prepare($param);方法中得到正确的值），必须用global标识，否则出错
        global $command;//MUST
        global $keyword;//MUST
        global $kv;
        /*$content=$object['content'];
        $fromUserName=$object['fromusername'];
        $toUserName=$object['tousername'];*/
        //$content=$object->getRequest('content');
        $fromUserName="DEFAUT_USER";//$object->getRequest('fromusername');
        $toUserName="DEFAUT_USER";//$object->getRequest('tousername');
        
        $param = trim($content);
        //prepare($param)方法处理后，取出$command和$keyword
		$content = prepare($param);

		if($command ==1){//推送信息存储到kvdb
			//创建新浪KVDB对象
			$kv = new SaeKV();
			//初始化SaeKV对象
			$ret = $kv->init();
			
			$t=time();
			// 增加key-value
			$ret = $kv->add('msg.item.'.$t, $keyword);
			$ret = $kv->add('msg.from.'.$t, $fromUserName);//
			$ret = $kv->add('msg.to.'.$t, $fromUserName);//
			if(strlen($at)>0)$ret = $kv->add('msg.at.'.$t, $at);
		}	
        
		//首先精确，如果有匹配返回结果
		//此处依据prepare($param);最后返回值判断
		if($content !=null) return $content;//$param."->".
		//如果精确搜索没有匹配，则search($keyword)方法:定制搜索结果（模糊匹配搜索）
		//$keyword为全局变量，prepare($word) 方法中从$param中取出
        $content = "$keyword  [$command]\n------------------------------\n"
			.search($fromUserName, $toUserName, $keyword)
			."------------------------------\n基于微信的手机电脑间信息推送，关注微信公众号：LetItFly ";
		echo $content;
        return $content;//$param."=>".	
		
	}


	/*
	prepare方法专门负责解析指令及处理帮助、反馈等
	*/
	function prepare($word) 
	{        	
        //全局变量$command，$keyword（在prepare($param);方法中得到正确的值），必须用global标识，否则出错
        global $command;//MUST
        global $keyword;//MUST
        
        echo "Prepare:  $word <br/>";
        
        if((strcmp($word , "h")==0) or (strcmp($word , "0")==0) ){// || $word == 0 || $word == "0"
			$helpText="帮助：\n * 发送0或h获取帮助\n * 发送文本，推送到服务器（默认给本人微信账户）。\n *  数字指令1-2功能（参数用英文冒号:分隔）。\n * 1:收件人:文本，发送文本给指定收件人。\n * 2 获取本人电脑端或别人推送的信息。\n * 举例：\n输入1:小明: 推荐音乐http://url.cn/2qDEZT?q.mp3。[推送该音乐短链接给小明]";	
			return $helpText;
		}
        if(checkStr($word)==7){
			return "感谢你的反馈，我会把你的话转告我的主人的。相信主人很快会给你恢复:)";
        }
        //echo "Command  mode  ".$word[1];
        //echo "<br/>";		
        $pos=strpos($word,":");
        echo "Command pos:  $pos <br/>";
        
        if($pos !== false){// if($keyword[1] == ":"){
			$parts=explode(":" , $word);
			$command = $parts[0];
			$keyword = $parts[1];
			//$command =$keyword[0];
			//$keyword =substr($keyword, 2, strlen($keyword));
			echo "Command: $command <br/> Keyword: $keyword<br/>";
			
        }else{//不包含指令，用户输入为要查的词
			if((strcmp($word , "h")==0) OR (strcmp($word , "0")==0))
				$command = 0;//初帮助指令
			else	
				$command = 1;//初帮助指令以外的默认指令
			$keyword = $word;
        }
        return null;
	}


	function search($fromUser, $toUser, $word){
		$result="";
		//全局变量$command，$keyword（在prepare($param);方法中得到正确的值），必须用global标识，否则出错
		global $command;
		global $recordCount;
		
		echo "Search: $fromUser, $toUser,  $word<br/>";
		
		$kv = new SaeKV();
		//初始化SaeKV对象
		$ret = $kv->init();
		$ret = $kv->pkrget('msg.item', 100);

		 echo "<table border=0  cellPadding=0 cellspacing=0 >";
		 echo "<tr><td  nowrap class=\"frameCell\">ID</td><td  nowrap class=\"frameCell\">Key</td><td  nowrap class=\"frameCell\">Date</td><td class=\"frameCell\">Messgage</td>
			<td class=\"frameCell\">From</td><td class=\"frameCell\">To</td><td class=\"frameCell\">@</td><td class=\"frameCell\">Action</td></tr>";        
		 
		 $ckey="";
		 $i=0;
		 if(!empty($ret))  
         {              
            foreach($ret as $key=>$val)  
            {                
                $i=$i+1;
                if($i>$recordCount-5){
					$ckey=$key;
					$result=$result."|".$val;
				}
                //var_dump("key: ".$key." value: ".$val."<p>");
                //计算指定字符串在目标字符串中最后一次出现的位置
				//$spos = strrpos(".", $key);
				//取倒数n个字符
				//取key的后缀，即时间字符串
				//（例如key：msg.item.1409287879 后缀：1409287879
				$rail = substr($key,-10);
				$date = date("Y-m-d H:i:s", $rail) ;
				// 获得后缀为$rail的消息（msg.item）对应的发送源（msg.to）
				$_from = $kv->get("msg.from.".$rail);
				// 获得后缀为$rail的消息（msg.item）对应的发送对象（msg.to）
				$_to = $kv->get("msg.to.".$rail);
                // 获得后缀为$rail的消息（msg.item）对应的回复对象（msg.to）
				$_at = $kv->get("msg.at.".$rail);
                
                echo "<tr>";
                echo "<td class=\"frameCell\">".$i."</td>";
                echo "<td class=\"frameCell\" style=\"width:10%;word-break:break-all; \">".$key."</td>";
                echo "<td class=\"frameCell\">".$date."</td>";
                echo "<td id=\"code".$i."\"  class=\"frameCell\"  style=\"width:40%;word-break:break-all; \"  onclick=\"selectCell(this)\">".$val."</td>";
                echo "<td nowrap class=\"frameCell\" >".$_from."</td>";
                echo "<td nowrap class=\"frameCell\" >".$_to."</td>";
                echo "<td class=\"frameCell\" style=\"width:10%;word-break:break-all; \" >".$_at."</td>";
                
                /*onclick=\"submitForm()\"*/
                echo "<td nowrap class=\"frameCell\" >
					<a  href=\"connect.php?action=respond&from=".$_to."&to=".$_from."&at=".$key."\">Respond</a>&nbsp;
					<a href=\"kvdbManager.php?action=update&key=".$key."&value=".$val."\"   target=\"_blank\">Update</a>&nbsp;
					<a href=\"connect.php?action=delete&key=".$key."\">Delete</a> </td>";
					
				echo "</tr>";
				//<a  href=\"javascript:copycode($('code".$i."'));\">Copy</a>&nbsp;
                /*if($val['t']<time() && !empty($val['t']))  
                {  
                    unset($v[$key]);  
                    $this->delete($key);  
                }  
                else  
                {  
                    $v[$key] = $val['d'];  
                } */
            }  
        }  
		echo "</table>";
		// 获得消息总数（msg.count）
		//$_count = $kv->get("msg.count"); 
		echo "<p>共有 ".$i." 条消息。</p> ";
		
		//$ret = $kv->get('msg.item', 100);
		return $result;
	}
	/*
	判断数字，汉字和英文
	*/
	function checkStr($str){
		$output=-1;
		if(preg_match('[^\x00-\x7F]', $str)){//eregi
			$output=7;//'有中文字符串';
		}     
		/*    
		$a=ereg('['.chr(0xa1).'-'.chr(0xff).']', $str);
		$b=ereg('[0-9]', $str);
		$c=ereg('[a-zA-Z]', $str);
		
		if($a && $b && $c){ $output=7;//'汉字数字英文的混合字符串';}
		elseif($a && $b && !$c){ $output=6;//'汉字数字的混合字符串';}
		elseif($a && !$b && $c){ $output=5;//'汉字英文的混合字符串';}
		elseif(!$a && $b && $c){ $output=4;//'数字英文的混合字符串';}
		elseif($a && !$b && !$c){ $output=2;//'纯汉字';}
		elseif(!$a && $b && !$c){ $output=3;//'纯数字';}
		elseif(!$a && !$b && $c){ $output=1;//'纯英文';}
		
		*/
		return $output;
	}
	function traceHttp(){
		//初始化"saekv://log.content"
		//resetLog();
		
		//clear
		//file_put_contents("saekv://log.content","");
		
		logger("REMOTE_ADDR: ".$_SERVER["REMOTE_ADDR"].
			((strpos($_SERVER["REMOTE_ADDR"], "101.226"))?" FROM WEIXIN":" UNKNOWN IP"));
		logger("QUERY_STRING: ".$_SERVER["QUERY_STRING"]);
		//echo
		$old=file_get_contents("saekv://log.content");
		echo $old;
	}
	
	function logger($content){
		$old=file_get_contents("saekv://log.content");
		file_put_contents("saekv://log.content", $old.date('Y-m-d H:i:s    ').$content."<br>");
		//file_put_contents("log.html", date('Y-m-d H:i:s    ').$content."<br>",FILE_APPEND);	
	}
	
	function resetLog(){
		file_put_contents("saekv://log.content", "LOG FILE STORED ON KVDB<br>");
	}


?>
<a href="readme.html" targent="_blank">关于KVDB消息服务器的说明</a><br>
<a href="MessageManager.php?action=resetlog" targent="_blank">ResetLog</a>
</div>
<div id="rightBar"></div>
</div>
</body>
</html>