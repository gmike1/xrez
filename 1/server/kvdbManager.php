<html>
<head>
    <title>XREZ:KVDB-Manager</title>
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
		<tr><td style="border:dotted 0px blue;color:#4D8606;font-family:arial;font-size:20px;text-align:right;">KV</td>
		<td style="color: #f60;font-family:arial;font-size:20px;text-align:left;">DB</td></tr>
		<tr><td colspan=2 style="color:#494949;font-family:arial;font-size:10px;">Manager</td></tr>	
	</table><br>
<?php 
$action="";
$from='TanGuodong'; 
$to='JiangTing'; 
$msg='How is everything going on?';
$key=''; 
$value=''; 
$at='';

if(isset($_GET["action"]))$action=$_GET["action"];
if(isset($_GET["from"]))	$to=$_GET["from"];
if(isset($_GET["to"]))	$to=$_GET["to"];
if(isset($_GET["msg"]))	$msg=$_GET["msg"];
if(isset($_GET["key"]))	$key=$_GET["key"];
if(isset($_GET["value"]))	$value=$_GET["value"];
if(isset($_GET["at"]))	$at=$_GET["at"];

?>
	<form action="kvdbManager.php" method="get">
		<table style="width:100%">
        <tr><td class="frameCell" style="width:10%">From: </td>
        <td class="frameCell"><input type="text" name="from" value="<?php echo $from; ?>" style="width:100%;"/></td></tr>
        <tr><td class="frameCell" style="width:10%">To: </td>
        <td  class="frameCell"><input type="text" name="to" value="<?php echo $to; ?>" style="width:100%;"/></td></tr>
		<tr><td class="frameCell" style="width:10%">Message:</td>
		<td  class="frameCell" style="width:80%;"><input type="text" name="msg" value="<?php echo $msg; ?>" style="width:100%;"/></tr>
		<tr><td class="frameCell" style="width:10%">Key: </td>
        <td  class="frameCell"><input type="text" name="key" value="<?php echo $key; ?>" style="width:100%;"/></td></tr>
        <tr><td class="frameCell" style="width:10%">Value: </td>
        <td  class="frameCell"><input type="text" name="value" value="<?php echo $value; ?>" style="width:100%;"/></td></tr>
		<tr><td colspan=2>&nbsp;</td></tr>
		
		<tr>
		<td colspan=2>
		<input type="submit" name="action" value="Add"/>
		<input type="submit" name="action" value="Update"/>
		<input type="submit" name="action" value="Delete"/>
		</td>
		</tr>
		</table>
	</form>
<?php

 //创建新浪KVDB对象
 $kv = new SaeKV();
 //初始化SaeKV对象
 $ret = $kv->init();
 
 //增加key-value
 if(strcasecmp($action, "add")==0){
 // 增加key-value
 $ret = $kv->add($key, $value);
 //var_dump($ret);
 //$ret = $kv->add('msg.to.'.$t, $to);
 //var_dump($ret);
 }
 
 //回复信息。涉及msg.item, msg.from,msg.to,msg.at 4类数据更新
 if(strcasecmp($action, "respond")==0){
 
 }
 //替换key-value只在key存在时起作用
 if($action =="update"){
 //$t=time();
 // 替换key-value
 $ret = $kv->replace($key, $value);
 } 
 if($action =="delete"){
 $ret = $kv->delete($key);
 }
 //开始枚举更新后的key-value
 $ret = $kv->pkrget('msg', 100);

 echo "<table border=0  cellPadding=0 cellspacing=0  style=\"width:100%\">
	<td class=\"frameCell\" style=\"font-size: 8\">Key</td><td class=\"frameCell\" style=\"font-size: 8\">Value</td><td class=\"frameCell\" style=\"font-size: 8\">Action</td>";
         
 if(!empty($ret))  
        {  
            foreach($ret as $key=>$val)  
            {
                $status = "√";
                if(strpos($key,"msg.item")===false){
                $rail = substr($key,-10);
                $ret = $kv->get("msg.item.".$rail);
                if($ret===false)$status = "×";
                }
                
                //var_dump("key: ".$key." value: ".$val."<p>");
                echo "<tr>";
                echo "<td class=\"frameCell\" style=\"width:30%;font-size: 8\">".$key."</td>";
                echo "<td nowrap class=\"frameCell\" style=\"width:auto;font-size: 8;\">".$val."</td>";
                echo "<td nowrap class=\"frameCell\" >
					<a href=\"kvdbManager.php?action=update&key=".$key."&value=".$val."\"   target=\"_blank\">Update</a>&nbsp;
					<a href=\"kvdbManager.php?action=delete&key=".$key."\">Delete</a>".$status." </td>";            
                
				echo "</tr>";
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
 echo '<br><sapan style=\"width:auto;font-size: 8;\">显示√的为自检之后发现正确无误的消息；显示×的为自检之后发现已经没有一致性（孤立/损坏）的消息。</span>'; 
 // 删除key-value
 /*$ret = $kv->delete('abc');
 var_dump($ret);*/
?>
</div>
<div id="rightBar"></div>
</div>
</body>
</html>
