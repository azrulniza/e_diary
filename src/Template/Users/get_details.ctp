<?php
	$data = new \stdClass;
	$data->designations = $designations;
	$data->users = $users;
	echo json_encode($data);
?>