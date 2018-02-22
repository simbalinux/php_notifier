<?php
require_once(__DIR__ . '/../vendor/autoload.php');
$hostsToFind = require_once(__DIR__ . '/../config/hosts.php');
$logger = new SimpleLogger\File(__DIR__ . '/../log/simplelogger.log');
$config = require(__DIR__ . '/../config/main.php');
const EVERYTHING_IS_BROKEN = "DEGRADED";

$sendEmail = function ($subject, $body) use ($logger, $config) {
    $logger->info('DEGRADED STATUS. Try to Send Mail');
    $mail = new \PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();

    $mail->SMTPAuth = true;
    $mail->Host = $config['smtp']['host'];
    $mail->Port = $config['smtp']['port'];
    $mail->Username = $config['smtp']['username'];
    $mail->Password = $config['smtp']['password'];
    $mail->Mailer = "smtp";
    $mail->WordWrap = $config['smtp']['wordwrap'];
    $mail->SMTPSecure = $config['smtp']['smtpsecure'];

    $mail->From = $config['sender']['email'];
    $mail->FromName = $config['sender']['name'];

    if ($config['smtp']['smtpsecure'] == 'tls') {
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
    }
    $mail->addAddress($config['recipient']['email']);

    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->isHTML(true);

    if (!$mail->send()) {
        $message = "Mailer Error: " . $mail->ErrorInfo;
        $logger->error($message);
    } else {
        $message = "Email has been sent to {$config['recipient']['email']}";
        $logger->info($message);
    }
    echo $message;
};

$sendNotificationIfHostFailed = function () use ($logger, $hostsToFind, $sendEmail) {
    //* check if hostname has written to the log if !; then email hostname
    $lines = [];
    $fp = fopen("../log/simplelogger.log", "r");

    //tail off last x lines of the log file
    while (!feof($fp)) {
        $line = fgets($fp);
        array_push($lines, $line);
        if (count($lines) > 60) {
            array_shift($lines);
        }
    }
    fclose($fp);
    //list of hosts to search for
    foreach ($lines as $inputRow) {
        if (preg_match('/&hostname=(.+)$/', $inputRow, $token)) {
            $host = $token[1];
            if (array_key_exists($host, $hostsToFind)) {
                $hostsToFind[$host]++;
            }
        }
    }

    foreach ($hostsToFind as $host => $found) {
        if ($found) {
            continue;
        }
        echo "Host not found : " . $host . "\n";

        $sendEmail(
            "Script failure:  {$host}",
            "LSI MegaRAID CLI monitoring system has detected a script failure from {$host} "
        );
    }
};

$sendNotification = function () use ($logger, $sendEmail) {
    if (array_key_exists("status", $_GET) && array_key_exists("unit_status", $_GET) && array_key_exists("hostname", $_GET)) {
        $status = urldecode($_GET["status"]);
        $unitStatus = str_replace("\n", "<br>", urldecode($_GET["unit_status"]));
        $hostname = urldecode($_GET["hostname"]);

        if ($status == EVERYTHING_IS_BROKEN) {
            $sendEmail(
                "RAID Degraded on  {$hostname}",
                "LSI MegaRAID CLI has detected a RAID degradation on {$hostname} <br> Disk Status: <br> {$unitStatus}"
            );
        } else {
            $message = 'Wrong status! (Not "' . EVERYTHING_IS_BROKEN . '")';
            echo $message;
            $logger->alert($message);
        }
    } else {
        $message = "Wrong params! (status, unit_status or hostname does not present in GET)";
        echo $message;
        $logger->alert($message);
    }
};

try {
    $logger->info('Request with params ' . json_encode($_REQUEST));
    $logger->info($_SERVER['QUERY_STRING']);
    $sendNotification();
    $sendNotificationIfHostFailed();
} catch (Exception $e) {
    $logger->error('Error: ' . $e . $message);
}
