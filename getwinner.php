<?php 

require_once 'vendor/autoload.php';
require_once('functions.php');

$winners = dbGetWinner();

$resAddWinner = 0;
if (!empty($winners)) {
	$resAddWinner = dbAddWinner($winners);
}

if ($resAddWinner === 1) {
	$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
	$transport->setUsername("keks@phpdemo.ru");
	$transport->setPassword("htmlacademy");

	$mailer = new Swift_Mailer($transport);

	$logger = new Swift_Plugins_Loggers_ArrayLogger();
	$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

	foreach ($winners as $key => $value) {
	    $message = new Swift_Message();
	    $message->setSubject("Ваша ставка победила");
	    $message->setFrom(['keks@phpdemo.ru' => 'Yeticave']);
	    $message->setBcc($value['user_email']);

	    $winner_user = $value['user_name'];
	    $winner_lot = $value['lot_id'];
	    $winner_name = $value['lot_name'];

	    $msg_content = include_template('email.php', ['winner_user' => $winner_user, 'winner_lot' => $winner_lot, 
	    														'winner_name' => $winner_name]);
	    $message->setBody($msg_content, 'text/html');

	    $resultSendMails = $mailer->send($message);

	 }

	 if ($resultSendMails) {
	 	print("Рассылка успешно отправлена");
	 }
	 else {
	    print("Не удалось отправить рассылку: "  . $logger->dump());
	 }
}

?>