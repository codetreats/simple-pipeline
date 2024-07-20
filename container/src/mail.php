<?php
function mymail($subject,$message)
{

$host = getenv("MAIL_HOST");
$username = getenv("MAIL_USER");
$password = getenv("MAIL_PASSWORD");
$from_mail = getenv("MAIL_FROM");
$to = getenv("MAIL_TO");
$name = getenv("MAIL_FROMNAME");
if ($name !== false) {
    $from = "$name <$from_mail>";
} else {
    $from = "$from_mail";
}


// Open an SMTP connection
$cp = fsockopen ($host, 25, $errno, $errstr, 1);
if (!$cp)
return "Failed to even make a connection";
$res=fgets($cp,256);
if(substr($res,0,3) != "220") return "Failed to connect";

// Say hello...
fputs($cp, "HELO networkmonitor\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "250") return "Failed to Introduce";

// perform authentication
fputs($cp, "auth login\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "334") return "Failed to Initiate Authentication";

fputs($cp, base64_encode($username)."\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "334") return "Failed to Provide Username for Authentication";

fputs($cp, base64_encode($password)."\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "235") return "Failed to Authenticate";

// Mail from...
fputs($cp, "MAIL FROM: <$from_mail>\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "250") return "MAIL FROM failed";

// Rcpt to...
fputs($cp, "RCPT TO: <$to>\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "250") return "RCPT TO failed";

// Data...
fputs($cp, "DATA\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "354") return "DATA failed";

// Send To:, From:, Subject:, other headers, blank line, message, and finish
// with a period on its own line (for end of message)
fputs($cp, "To: $to\r\nFrom: $from\r\nSubject: $subject\r\n\r\n$message\r\n.\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "250") return "Message Body Failed";

// ...And time to quit...
fputs($cp,"QUIT\r\n");
$res=fgets($cp,256);
if(substr($res,0,3) != "221") return "QUIT failed";

return true;
}

mymail($argv[1], $argv[2]);
?>
