<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?= $title ?></title>
</head>
<body>
	<div id="header">
		<h2><?= $header_title ?></h2>
<?php if (isset($message)) { ?>
		<p><?= $message ?></p>
<?php } ?>
	</div>
