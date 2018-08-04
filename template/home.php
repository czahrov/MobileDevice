<?php
	/*
Array
(
    [0] => Acer Liquid E600
    [1] => Android 4.4
    [2] => 5.0
    [3] => 480x854
    [4] => 1.2GHz quad-core
    [5] => 1024
    [6] => 4
    [7] => 2500
    [8] => 8-megapixel
)

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
	
	if( isset( $_GET['filter'] ) ){
		// print_r( $_GET );
		// print_r( $_POST );
		echo json_encode( getDCSV()->getItems( $_POST ) );
		/* print_r( array_slice( getDCSV()->getItems( array( 
			'diagonal' => array(
				'min' => 6,
			),
		) ), 0, 30 ) ); */
		exit;
	}
	
	getHeader();
?>
<div id='home' class='container'>
	<div id='side' class='container'>
		<script>
			var ranges = JSON.parse('<?php
				$dc = getDCSV();
				$ranges = $dc->getRange();
				echo json_encode( $ranges );
			?>');
		</script>
		<div class='scroller'>
			<div class='icon'></div>
		</div>
		<div class='row model'>
			<div class='col-12 h6'>
				Nazwa modelu
			</div>
			<div class='col-12'>
				<input type='text' name='model' placeholder='Szukaj'>
			</div>
			
		</div>
		<div class='row ram'>
			<div class='col-12 h6'>
				Pamięć RAM
			</div>
			<div class='col-12'>
				<div class='slider'></div>
				<div class='text text-center'></div>
			</div>
			
		</div>
		<div class='row diagonal'>
			<div class='col-12 h6'>
				Przekątna ekranu
			</div>
			<div class='col-12'>
				<div class='slider'></div>
				<div class='text text-center'></div>
			</div>
			
		</div>
		<div class='row battery'>
			<div class='col-12 h6'>
				Pojemność baterii
			</div>
			<div class='col-12'>
				<div class='slider'></div>
				<div class='text text-center'></div>
			</div>
			
		</div>
		<div class='row resolution'>
			<div class='col-12 h6'>
				Rozdzielczość ekranu
			</div>
			<div class='d-flex flex-wrap'>
				<?php foreach( $ranges['resolution'] as $num => $res ): ?>
				<div class='single  d-flex align-items-center justify-content-start col-12 col-sm-6'>
					<input id='res<?php printf( '%02s', $num ); ?>' type='checkbox' name='resolution[]' value='<?php echo $res; ?>'>
					<label for='res<?php printf( '%02s', $num ); ?>'>
						<?php echo $res; ?>
					</label>
					
				</div>
				<?php endforeach; ?>
			</div>
			
		</div>
		
	</div>
	<div class='view'>
		<div class='top container'>
			<div id='switch' class='d-none d-md-flex'>
				<div class='icon list' title='Widok listy'></div>
				<div class='icon grid' title='Widok siatki'></div>
				
			</div>
			<div class='title h5 text-center'>
				Znaleziono <span></span> wyników
			</div>
			
		</div>
		<div class='body d-flex flex-wrap'>
			<?php
				// foreach( getDCSV()->getItems() as $item ){
					// echo $item;
				// }
			?>
		</div>
		
	</div>
	
</div>

<?php getFooter(); ?>