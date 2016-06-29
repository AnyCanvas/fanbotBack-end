<?php
// Analizar la información del evento en forma de json
$body = @file_get_contents('php://input');
$data = json_decode($body,true);


//if ($charge->type == 'charge.paid'){
	if( !(isset($data['data']) ) ){

	}
//}

?>