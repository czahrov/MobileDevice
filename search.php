<?php
require __DIR__ . "/functions.php";
if( !empty( $_GET ) ){
	// print_r( $_GET );
	
	// $dc = new DevicesCSV( __DIR__ . "/php/Data for Test - Frontend Developer Pumox GmbH.csv" );
	$dc = getDCSV();
	$word = $_GET['term'];
	// var_dump( $word );
	// echo json_encode( $dc->getNames( $word ) );
	echo json_encode( array_slice( $dc->getNames( $word ), 0, 20 ) );
	
}
	
