<html>
<head>
    <title>MessageManager</title>
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
		<tr><td colspan=2 style="color:#494949;font-family:arial;font-size:10px;">&nbsp;��&nbsp;ͨ&nbsp;��&nbsp;��&nbsp;��</td></tr>	
	</table><br>
	
<?php	
	/*
	Version 1.0 20150616 MessageManager.php
	�ۺϵ�function process($content)�������ݲ����ش��ı���΢��ģ����������server.php��$this->responseText()��������ִ��
	����΢�Ź���ƽ̨ PHP SDK
	�ο�http://sae.sina.com.cn/?m=apps&a=detail&aid=162
	*/
	
	//ȫ�ֱ���$command��$keyword����prepare($param);�����еõ���ȷ��ֵ���������б�����global��ʶ���������
	$command=0;
	$keyword=null;
	$recordCount=1;
	
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
	
	//DEBUG
	if (isset($_GET['request'])) {
		$param = $_GET['request'];
		process($param);
	}
	
	
	//��������KVDB����
	$kv = new SaeKV();
	//��ʼ��SaeKV����
	$ret = $kv->init();


	/*
	process�����������뷵�������
	*/
	function process($object) 
	{
        //ȫ�ֱ���$command��$keyword����prepare($param);�����еõ���ȷ��ֵ����������global��ʶ���������
        global $command;//MUST
        global $keyword;//MUST
        
        $content=$this->getRequest('content');
        $fromUserName=$this->getRequest('fromusername');
        $toUserName=$this->getRequest('tousername');
        
        $param = trim($content);
        //prepare($param)���������ȡ��$command��$keyword
		$content = prepare($param);

		if($command ==1){//������Ϣ�洢��kvdb
			$t=time();
			// ����key-value
			$ret = $kv->add('msg.item.'.$t, $keyword);
			$ret = $kv->add('msg.from.'.$t, $fromUserName);
			$ret = $kv->add('msg.to.'.$t, $fromUserName);
			if(strlen($at)>0)$ret = $kv->add('msg.at.'.$t, $at);
		}	
        
		//���Ⱦ�ȷ�������ƥ�䷵�ؽ��
		//�˴�����prepare($param);��󷵻�ֵ�ж�
		if($content !=null) return $content;//$param."->".
		//�����ȷ����û��ƥ�䣬��search($keyword)����:�������������ģ��ƥ��������
		//$keywordΪȫ�ֱ�����prepare($word) �����д�$param��ȡ��
        $content = "$keyword  [$command]\n------------------------------\n"
			.search($fromUserName, $toUserName, $keyword)
			."------------------------------\n����΢�ŵ��ֻ����Լ���Ϣ���ͣ���ע΢�Ź��ںţ�LetItFly ";

        return $content;//$param."=>".	
		
	}


	/*
	prepare����ר�Ÿ������ָ����������������
	*/
	function prepare($word) 
	{        	
        //ȫ�ֱ���$command��$keyword����prepare($param);�����еõ���ȷ��ֵ����������global��ʶ���������
        global $command;//MUST
        global $keyword;//MUST
        
        echo "Prepare:  $word <br/>";
        
        if((strcmp($word , "h")==0) ||(strcmp($word , "0")==0) ){// || $word == 0 || $word == "0"
			$helpText="������\n * ����0��h��ȡ����\n * �����ı������͵���������Ĭ�ϸ�����΢���˻�����\n *  ����ָ��1-2���ܣ�������Ӣ��ð��:�ָ�����\n * 1:�ռ���:�ı��������ı���ָ���ռ��ˡ�\n * 2 ��ȡ���˵��Զ˻�������͵���Ϣ��\n * ������\n����1:С��: �Ƽ�����http://url.cn/2qDEZT?q.mp3��[���͸����ֶ����Ӹ�С��]";	
			return $helpText;
		}
        if(checkStr($word)==7){
			return "��л��ķ������һ����Ļ�ת���ҵ����˵ġ��������˺ܿ�����ָ�:)";
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
			
        }else{//������ָ��û�����ΪҪ��Ĵ�
			if((strcmp($word , "h")==0) OR (strcmp($word , "0")==0))
				$command = 0;//������ָ��
			else	
				$command = 1;//������ָ�������Ĭ��ָ��
			$keyword = $word;
        }
        return null;
	}


	function search($fromUser, $toUser, $word){
		$result="";
		//ȫ�ֱ���$command��$keyword����prepare($param);�����еõ���ȷ��ֵ����������global��ʶ���������
		global $command;
		global $recordCount;
		
		echo "Search: $fromUser, $toUser,  $word<br/>";
 
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
		
		//$ret = $kv->get('msg.item', 100);
		return $result;
	}
?>
<a href="readme.html" targent="_blank">����KVDB��Ϣ��������˵��</a>
</div>
<div id="rightBar"></div>
</div>
</body>
</html>