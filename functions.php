<?php

function getHeader(){
	require_once URL . "/header.php";
	
}

function getFooter(){
	require_once URL . "/footer.php";
	
}

class DevicesCSV{
	private $_file = null;
	private $_data = array();
	private $_ranges = array();
	
	public function __construct( $filePath = null ){
		if( file_exists( $filePath ) ){
			$this->_file = fopen( $filePath, 'r' );
			$this->_readFile();
		}
		else{
			$this->_file = null;
		}
		
	}
	
	public function __destruct(){
		if( $this->_file !== null ) fclose( $this->_file );
		
	}
	
	private function _readFile(){
		/* 
		Array
(
    [0] => nazwa
    [1] => system
    [2] => przekątna ekranu
    [3] => rozdzielczość ekranu
    [4] => procesor
    [5] => pamięć RAM
    [6] => pamięć wewnętrzna
    [7] => pojemność baterii
    [8] => aparat
)
		*/
		while( !feof( $this->_file ) ){
			$line = fgetcsv( $this->_file, 0, ";" );
			
			$this->_data[] = array(
				'name' => $line[0],
				'os' => $line[1],
				'diagonal' => (float)$line[2],
				'resolution' => $line[3],
				'processor' => $line[4],
				'memory' => (int)$line[5],
				'storage' => (int)$line[6],
				'battery' => (int)$line[7],
				'camera' => $line[8],
			);
		}
		
		$this->_calcRanges();
		
	}
	
	public function getNames( $word = null ){
		$list = array();
		foreach( $this->_data as $device ){
			if( $word === null or $word !== null && stripos( $device['name'], $word ) !== false ){
				$list[] = $device['name'];
			}
			
		}
		
		sort( $list );
		
		return $list;
	}
	
	private function _calcRanges(){
		$list = array(
			'diagonal' => array(
				'min' => null,
				'max' => null,
			),
			'memory' => array(
				'min' => null,
				'max' => null,
			),
			'battery' => array(
				'min' => null,
				'max' => null,
			),
			'resolution' => array(),
			
		);
		foreach( $this->_data as $device ){
			foreach( array( 'diagonal', 'memory', 'battery' ) as $param ){
				if( !empty( $device[ $param ] ) and ( $list[ $param ]['max'] === null or $list[ $param ]['max'] < $device[ $param ] ) ) $list[ $param ]['max'] = $device[ $param ];
				if( !empty( $device[ $param ] ) and ( $list[ $param ]['min'] === null or $list[ $param ]['min'] > $device[ $param ] ) ) $list[ $param ]['min'] = $device[ $param ];
			}
			if( !empty( $device['resolution'] ) ){
				if( array_key_exists( $device['resolution'], $list['resolution'] ) ){
					$list['resolution'][ $device['resolution'] ]++;
				}
				else{
					$list['resolution'][ $device['resolution'] ] = 1;
				}
				
			}
			
		}
		
		$t = array();
		
		foreach( $list['resolution'] as $res => $v ){
			if( $v > 10 ) $t[] = $res;
		}
		
		$list['resolution'] = $t;
		
		// sort( $list['resolution'] );
		usort( $list['resolution'], function( $x, $y ){
			$tx = sscanf( $x, '%ux%u' );
			$ty = sscanf( $y, '%ux%u' );
			
			if( $tx[0] == $ty[0] ){
				if( $tx[1] == $ty[1] ){
					return 0;
				}
				else{
					return $tx[1] < $ty[1]?( 1 ):( -1 );
				}
				
			}
			else{
				return $tx[0] < $ty[0]?( 1 ):( -1 );
				
			}
			
		} );
		
		$this->_ranges = $list;
		
	}
	
	public function getRange(){
		return $this->_ranges;
	}
	
	public function getItems( $criteria = array() ){
		/* wynikowa tablica znalezionych urządzeń (HTML) */
		$ret = array();
		
		/* standardowe kryteria wyszukiwania */
		$t_crit = array(
			'name' => '',
			'diagonal' => array(
				'min' => $this->_ranges['diagonal']['min'],
				'max' => $this->_ranges['diagonal']['max'],
			),
			'memory' => array(
				'min' => $this->_ranges['memory']['min'],
				'max' => $this->_ranges['memory']['max'],
			),
			
			'battery' => array(
				'min' => $this->_ranges['battery']['min'],
				'max' => $this->_ranges['battery']['max'],
			),
			
			'resolution' => array(),
		);
		
		/* uzupełnione kryteria wyszukiwania */
		$criteria['name'] = empty( $criteria['name'] )?( $t_crit['name'] ):( $criteria['name'] );
		$criteria['diagonal'] = empty( $criteria['diagonal'] )?( $t_crit['diagonal'] ):( array_merge( $t_crit['diagonal'], $criteria['diagonal'] ) );
		$criteria['memory'] = empty( $criteria['memory'] )?( $t_crit['memory'] ):( array_merge( $t_crit['memory'], $criteria['memory'] ) );
		$criteria['battery'] = empty( $criteria['battery'] )?( $t_crit['battery'] ):( array_merge( $t_crit['battery'], $criteria['battery'] ) );
		$criteria['resolution'] = empty( $criteria['resolution'] )?( $t_crit['resolution'] ):( array_merge( $t_crit['resolution'], $criteria['resolution'] ) );
		
		// var_dump( $criteria );
		
		/* tablica odfiltrowanych produktów */
		$found = array();
		foreach( $this->_data as $arg ){
			// var_dump( $criteria );
			// var_dump( $arg );
			
			$t1 = $arg['diagonal'] >= $criteria['diagonal']['min'] and $arg['diagonal'] <= $criteria['diagonal']['max'];
			$t2 = $arg['memory'] >= $criteria['memory']['min'] and $arg['memory'] <= $criteria['memory']['max'];
			$t3 = $arg['battery'] >= $criteria['battery']['min'] and $arg['battery'] <= $criteria['battery']['max'];
			$t4 = ( empty( $criteria['resolution'] ) or in_array( $arg['resolution'], $criteria['resolution'] ) );
			$t5 = ( empty( $criteria['name'] ) or stripos( $arg['name'], $criteria['name'] ) !== false );
			
			// var_dump( array(
				// 'diagonal' => $t1,
				// 'memory' => $t2,
				// 'battery' => $t3,
				// 'resolution' => $t4,
				// 'name' => $t5,
			// ) );
			
			if( $t1 and $t2 and $t3 and $t4 and $t5 ) $found[] = $arg;
			
		}
		
		foreach( $found as $item ){
			$params = array(
				array(
					"name" => "diagonal",
					"value" => $item['diagonal'] . "''",
					"title" => "Przekątna wyświetlacza",
				),
				array(
					"name" => "resolution",
					"value" => $item['resolution'] . "px",
					"title" => "Rozdzielczość wyświetlacza",
				),
				array(
					"name" => "memory",
					"value" => $item['memory'] . "MB",
					"title" => "Pamięć RAM",
				),
				array(
					"name" => "system",
					"value" => $item['os'],
					"title" => "System operacyjny",
				),
				array(
					"name" => "battery",
					"value" => $item['battery'] . "mAh",
					"title" => "Pojemność baterii",
				),
				array(
					"name" => "storage",
					"value" => $item['storage'] . "GB",
					"title" => "Pamięć wewnętrzna",
				),
				
			);
			
			$params_text = "";
			
			foreach( $params as $param ){
				$params_text .= sprintf(
					'<div class="param %s d-flex col-6 align-items-center" title="%s">
						<div class="icon"></div>
						<div class="value">
							%s
						</div>
						
					</div>',
					$param['name'],
					$param['title'],
					in_array( $param["value"], array( 0, '' ) )?( 'n/a' ):( $param["value"] )
					
				);
				
			}
			
			$ret[] = sprintf(
				'<div class="item flex-wrap col-12 col-lg-6 col-xl-4">
					<div class="name text-center h5 col-12">
						%s
					</div>
					<div class="box d-flex flex-column flex-md-row">
						<div class="img col-md-3"></div>
						<div class="details d-flex flex-wrap no-gutters">
							%s
						</div>
					</div>
					
				</div>',
				$item['name'],
				$params_text
				
			);
			
		}
		
		// return $found;
		return $ret;
	}
	
}

function getDCSV(){
	static $instance = null;
	if( $instance === null ) $instance = new DevicesCSV( __DIR__ . "/php/Data for Test - Frontend Developer Pumox GmbH.csv" );
	return $instance;
}

