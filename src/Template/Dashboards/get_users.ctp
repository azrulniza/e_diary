<?php
	$data = new \stdClass;
	$data->users = $users;
	echo json_encode($data);
?>