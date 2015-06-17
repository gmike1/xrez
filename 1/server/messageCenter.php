<head>
    <title>XREZ:Message-Center</title>
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
	
	<script language="JavaScript">
	function selectRow(obj){
	  if(event.srcElement.tagName=="TD"){
	  curRow=event.srcElement.parentElement;
	  curRow.style.background="blue";
	  alert("���ǵ�"+(curRow.rowIndex+1)+"��");
	  }
	}
	function selectCell(obj){
		if(event.srcElement.tagName=="TD"){
			var rng = document.body.createTextRange();
			rng.moveToElementText(obj);
			rng.scrollIntoView();
			rng.select();
			rng.execCommand("Copy");
			rng.collapse(false);
		}	
	}
	//invalid
	function copycode(obj) {
		//if(is_ie && obj.style.display != 'none') {
			var rng = document.body.createTextRange();
			rng.moveToElementText(obj);
			rng.scrollIntoView();
			rng.select();
			rng.execCommand("Copy");
			rng.collapse(false);
		//}
	}
	function submitForm()
	{
		var from=headerForm.from.value;
		var to=headerForm.to.value;
		var msg=headerForm.word.value;
		var at=headerForm.at.value;
		window.location='connect.php?action=Send&from='+from+'&to='+to+'&word='+msg+'&at='+at;
	}
	</script>
</head>
<body>
<div id="wrapper" style=" border:dotted 0px grey;">
<div id="leftBar" style="border:dotted 0px brown;"></div>
<div id="main" style="border:dotted 0px grey;">
	<!--LOGO style="border:dotted 0px blue;" -->
	<table border=0 cellPadding=0 cellspacing=0 >
		<tr><td style="border:dotted 0px blue;color:#4D8606;font-family:arial;font-size:20px;text-align:right;">Message</td>
		<td style="color: #f60;font-family:arial;font-size:20px;text-align:left;">Center</td></tr>
		<tr><td colspan=2 style="color:#494949;font-family:arial;font-size:10px;">&nbsp;��&nbsp;ͨ&nbsp;��&nbsp;��&nbsp;��</td></tr>	
	</table><br>
	
<?php
	$action="";
	$from='Tan Guodong'; 
	$to='Jiang Ting'; 
	$word='';
	$at='';
	$key='';
	if(isset($_GET["action"]))$action=$_GET["action"];
	if(isset($_GET["from"]))	$from=$_GET["from"];
	if(isset($_GET["to"]))	$to=$_GET["to"];
	if(isset($_GET["word"]))	$word=$_GET["word"];
	if(isset($_GET["at"]))	$at=$_GET["at"];
	if(isset($_GET["key"]))	$key=$_GET["key"];
	//echo "<br>";

?>
	<form name="headerForm" action="messageCenter.php" method="get">
		<table style="width:100%">
		<tr><td class="frameCell" style="width:10%">From: </td>
        <td class="frameCell"><input type="text" name="from" value="<?php echo $from; ?>" style="width:100%;"/></td></tr>
        <tr><td class="frameCell" style="width:10%">To: </td>
        <td  class="frameCell" style="width:80%;"><input type="text" name="to" value="<?php echo $to; ?>" style="width:100%;"/></td></tr>
		<tr>
		<td class="frameCell" style="width:10%">Message:</td>
		<td  class="frameCell" style="width:80%;"><input type="text" name="word" value="<?php echo $word; ?>" style="width:100%;"/></tr>
		<tr>
		<tr>
		<td class="frameCell" style="width:10%">@:</td>
		<td  class="frameCell" style="width:80%;"><input type="text" name="at" value="<?php echo $at; ?>" style="width:100%;"/></tr>
		<tr><td colspan=2>&nbsp;</td></tr>
		
		<tr><td colspan=2>
		<input type="submit" name="action" value="Send"/>
		<input type="submit" name="action" value="Receive"/>
		</td>
		</tr>
		</table>
	</form>
	
<?php
 //��������KVDB����
 $kv = new SaeKV();
 // ��ʼ��SaeKV����
 $ret = $kv->init();
 //var_dump($ret);
 //echo '<p>';
 
 
 if($action =="Send"){
	$t=time();
	// ����key-value
	$ret = $kv->add('msg.item.'.$t, $word);
	$ret = $kv->add('msg.from.'.$t, $from);
	$ret = $kv->add('msg.to.'.$t, $to);
	if(strlen($at)>0)
	$ret = $kv->add('msg.at.'.$t, $at);
 }
 /*
 ɾ��һ����Ϣ��4������
 */
 if($action =="delete"){
	// ɾ����׺Ϊ$rail����Ϣ��msg.item����Ӧ����Ϣ����
	$ret = $kv->delete($key);
	$rail = substr($key,-10);
	// ɾ����׺Ϊ$rail����Ϣ��msg.item����Ӧ�ķ���Դ��msg.from��
	$ret = $kv->delete("msg.from.".$rail);
	// ɾ����׺Ϊ$rail����Ϣ��msg.item����Ӧ�ķ��Ͷ���msg.to��
	$ret = $kv->delete("msg.to.".$rail);
	// ɾ����׺Ϊ$rail����Ϣ��msg.item����Ӧ�Ļظ�����msg.at��
	$ret = $kv->delete("msg.at.".$rail);
	//ÿɾ��һ������¼��������1
	$recordCount=$kv->get("msg.recordCount");
	$kv->set("msg.recordCount", ($recordCount-1));  
 }
 
 $ret = $kv->pkrget('msg.item', 100);

 echo "<table border=0  cellPadding=0 cellspacing=0 >";
 echo "<tr><td  nowrap class=\"frameCell\">ID</td><td  nowrap class=\"frameCell\">Key</td><td  nowrap class=\"frameCell\">Date</td><td class=\"frameCell\">Messgage</td>
	<td class=\"frameCell\">From</td><td class=\"frameCell\">To</td><td class=\"frameCell\">@</td><td class=\"frameCell\">Action</td></tr>";        
 if(!empty($ret))  
        {  
            $i=0;
            foreach($ret as $key=>$val)  
            {                
                $i=$i+1;
                //var_dump("key: ".$key." value: ".$val."<p>");
                //����ָ���ַ�����Ŀ���ַ��������һ�γ��ֵ�λ��
				//$spos = strrpos(".", $key);
				//ȡ����n���ַ�
				//ȡkey�ĺ�׺����ʱ���ַ���
				//������key��msg.item.1409287879 ��׺��1409287879
				$rail = substr($key,-10);
				$date = date("Y-m-d H:i:s", $rail) ;
				// ��ú�׺Ϊ$rail����Ϣ��msg.item����Ӧ�ķ���Դ��msg.to��
				$_from = $kv->get("msg.from.".$rail);
				// ��ú�׺Ϊ$rail����Ϣ��msg.item����Ӧ�ķ��Ͷ���msg.to��
				$_to = $kv->get("msg.to.".$rail);
                // ��ú�׺Ϊ$rail����Ϣ��msg.item����Ӧ�Ļظ�����msg.to��
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
		// �����Ϣ������msg.count��
		//$_count = $kv->get("msg.count"); 
		echo "<p>���� ".$i." ����Ϣ��</p> ";    

?>
<a href="readme.html" targent="_blank">����KVDB��Ϣ��������˵��</a>
</div>
<div id="rightBar"></div>
</div>
</body>