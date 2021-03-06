<?php
    // PHP version of merchant.jsp
    //这是B2CAPI的php调用测试
    //作    者：刘明
    //创建时间：2008-12-02
?>

<html>
    <head>
        <title>商户订单测试</title>

        <meta http-equiv = "Content-Type" content = "text/html; charset=gb2312">
    </head>

    <?php
        //define("JAVA_DEBUG", true); //调试设置	
        //define("JAVA_HOSTS", "127.0.0.1:8080"); //设置javabridge监听端口，如果开启javabridge.jar设置的端口不是8080，可通过此语句更改
				define("JAVA_PIPE_DIR", null); 
        require_once("java/Java.inc"); //php调用java的接口，必须的
		
        $here=realpath(dirname($_SERVER["SCRIPT_FILENAME"]));

        if (!$here)
            $here=getcwd();

        java_set_library_path("$here/lib"); //设置java开发包路径
        java_set_file_encoding("GBK");      //设置java编码

        //获得java对象
        $BOCOMSetting=java("com.bocom.netpay.b2cAPI.BOCOMSetting");
        $client=new java("com.bocom.netpay.b2cAPI.BOCOMB2CClient");
        $ret=$client->initialize("C:/bocommjava/ini/B2CMerchant.xml");
		$ret = java_values($ret);
        if ($ret != "0")
            {
            $err=$client->getLastErr();
            //为正确显示中文对返回java变量进行转换，如果用java_set_file_encoding进行过转换则不用再次转换
            //$err = java_values($err->getBytes("GBK")); 
            $err=java_values($err);
            print "初始化失败,错误信息：" . $err . "<br>";
            exit(1);
            }

        //获得表单传过来的数据
        $interfaceVersion=$_REQUEST["interfaceVersion"];
        $merID=java_values($BOCOMSetting->MerchantID); //商户号为固定
        $orderid=$_REQUEST["orderid"];
        $orderDate=$_REQUEST["orderDate"];
        $orderTime=$_REQUEST["orderTime"];
        $tranType=$_REQUEST["tranType"];
        $amount=$_REQUEST["amount"];
        $curType=$_REQUEST["curType"];
        $orderContent=$_REQUEST["orderContent"];
        $orderMono=$_REQUEST["orderMono"];
        $phdFlag=$_REQUEST["phdFlag"];
        $notifyType=$_REQUEST["notifyType"];
        $merURL=$_REQUEST["merURL"];
        $goodsURL=$_REQUEST["goodsURL"];
        $jumpSeconds=$_REQUEST["jumpSeconds"];
        $payBatchNo=$_REQUEST["payBatchNo"];
        $proxyMerName=$_REQUEST["proxyMerName"];
        $proxyMerType=$_REQUEST["proxyMerType"];
        $proxyMerCredentials=$_REQUEST["proxyMerCredentials"];
        $netType=$_REQUEST["netType"];
        $source="";
				$issBankNo = $_REQUEST["issBankNo"];
        //连接字符串
        $source=$interfaceVersion . "|" . $merID . "|" . $orderid . "|" . $orderDate . "|" . $orderTime . "|"
                    . $tranType . "|" . $amount . "|" . $curType . "|" . $orderContent . "|" . $orderMono . "|"
                    . $phdFlag . "|" . $notifyType . "|" . $merURL . "|" . $goodsURL . "|" . $jumpSeconds . "|"
                    . $payBatchNo . "|" . $proxyMerName . "|" . $proxyMerType . "|" . $proxyMerCredentials . "|"
                    . $netType;

        $sourceMsg=new java("java.lang.String", $source);

        //下为生成数字签名
        $nss=new java("com.bocom.netpay.b2cAPI.NetSignServer");

        $merchantDN=$BOCOMSetting->MerchantCertDN;
        $nss->NSSetPlainText($sourceMsg->getBytes("GBK"));

        $bSignMsg=$nss->NSDetachedSign($merchantDN);
        $signMsg=new java("java.lang.String", $bSignMsg, "GBK");
    ?>

    <body bgcolor = "#FFFFFF" text = "#000000" onload = " javascript: form1.submit()">
        <form name = "form1" method = "post" action = "<?php echo(java_values($BOCOMSetting->OrderURL)); ?>">
            <input type = "hidden" name = "interfaceVersion" value = "<?php echo($interfaceVersion); ?>">
            <input type = "hidden" name = "merID" value = "<?php echo($merID); ?>">
            <input type = "hidden" name = "orderid" value = "<?php echo($orderid); ?>">
            <input type = "hidden" name = "orderDate" value = "<?php echo($orderDate); ?>">
            <input type = "hidden" name = "orderTime" value = "<?php echo($orderTime); ?>">
            <input type = "hidden" name = "tranType" value = "<?php echo($tranType); ?>">
            <input type = "hidden" name = "amount" value = "<?php echo($amount); ?>">
            <input type = "hidden" name = "curType" value = "<?php echo($curType); ?>">
            <input type = "hidden" name = "orderContent" value = "<?php echo($orderContent); ?>">
            <input type = "hidden" name = "orderMono" value = "<?php echo($orderMono); ?>">
            <input type = "hidden" name = "phdFlag" value = "<?php echo($phdFlag); ?>">
            <input type = "hidden" name = "notifyType" value = "<?php echo($notifyType); ?>">
            <input type = "hidden" name = "merURL" value = "<?php echo($merURL); ?>">
            <input type = "hidden" name = "goodsURL" value = "<?php echo($goodsURL); ?>">
            <input type = "hidden" name = "jumpSeconds" value = "<?php echo($jumpSeconds); ?>">
            <input type = "hidden" name = "payBatchNo" value = "<?php echo($payBatchNo); ?>">
            <input type = "hidden" name = "proxyMerName" value = "<?php echo($proxyMerName); ?>">
            <input type = "hidden" name = "proxyMerType" value = "<?php echo($proxyMerType); ?>">
            <input type = "hidden" name = "proxyMerCredentials" value = "<?php echo($proxyMerCredentials); ?>">
            <input type = "hidden" name = "netType" value = "<?php echo($netType); ?>">
            <input type = "hidden" name = "merSignMsg" value = "<?php echo($signMsg); ?>">
            <input type = "hidden" name = "issBankNo" value="<?php echo($issBankNo); ?>">
        </form>
    </body>
</html>
