<?php
//密码加密
function md666 ($password){
    $salt="abcdefg1234567890";
    return md5(md5($password).$salt);
}
//手机号加密
function phone ($ph){
    substr($ph,0,3)."****".substr($ph,7,4);
}
//xss防范
function kill_xss ($string){
    include_once "./Public/Admin/htmlpurifier/HTMLPurifier.auto.php";
    // 生成配置对象
    $cfg = HTMLPurifier_Config::createDefault();
    // 以下就是配置：
    $cfg -> set('Core.Encoding', 'UTF-8');
    // 设置允许使用的HTML标签
    $cfg -> set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,br,p[style],span[style],img[width|height|alt|src]');
    // 设置允许出现的CSS样式属性
    $cfg -> set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
    // 设置a标签上是否允许使用target="_blank"
    $cfg -> set('HTML.TargetBlank', TRUE);
    // 使用配置生成过滤用的对象
    $obj = new HTMLPurifier($cfg);
    // 过滤字符串
    return $obj -> purify($string);
}
//发送CURL请求
function curl_request($url,$post=false,$data=[],$https=false){
    $ch=curl_init($url);
    if($post){
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if($https){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

//发送邮件
function send_mail($email,$subject,$body){
    require './Application/Tools/PHPMailer/PHPMailerAutoload.php';

    $mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // 设置使用SMTP协议服务
    $mail->Host = "smtp.126.com";  // 设置发件箱SMTP服务器
    $mail->SMTPAuth = true;                               // 开启SMTP权限认证
    $mail->Username = 'xgy58837518@126.com';              // 发件箱账号
    $mail->Password = 'gx1362457';                           // 发件箱密码
    $mail->SMTPSecure = 'tls';                            // 安全加密方式（tsl、ssl）
    $mail->Port = 25;                                    // 发送邮件使用的端口
    $mail->CharSet = "UTF-8";                              //设置文字编码

    $mail->setFrom('xgy58837518@126.com');         //设置发件人（昵称可选）
    $mail->addAddress($email);     // 添加一个收件人（昵称可选）

    $mail->isHTML(true);                         // 设置内容格式

    $mail->Subject = $subject;                  //邮件主题
    $mail->Body    = $body;     //主体内容

    if(!$mail->send()) {
        return $mail->ErrorInfo;
    } else {
        return true;
    }
}