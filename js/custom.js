$( function(){
	
	/* body - items */
	(function( body, found_text ){
		body
		.on({
			load: function( e, items ){
				var data = items.slice( 0, 36 );
				
				found_text.text( items.length );
				
				$.each( data, function( k, v ){
					$( v )
					.appendTo( body )
					.css( 'opacity', 0 )
					.addClass('new');
					
				} );
				
				/* usuwanie starych */
				body.find('.item:not(.new)').remove();
				
				/* pojawianie nowych */
				TweenMax.staggerFromTo(
					body.find('.item.new'),
					.1,
					{
						opacity: 0,
						scale: 0.8,
					},
					{
						opacity: 1,
						scale: 1,
						onComplete: function(){
							$( this.target )
							.removeClass('new')
							.attr( 'style', '' );
							
						},
					},
					.05
				);
				
			},
			
		});
		
	})
	(
		$('#home .view .body'),
		$('#home .view .top .title span')
	);
	
	/* side panel */
	(function( scroller, search, ram, ram_text, diagonal, diagonal_text, battery, battery_text, resolution ){
		scroller.click( function( e ){
			$('#home').toggleClass('scroll');
			
		} );
		
		search
		.autocomplete({
			source: 'search.php',
			minLength: 2,
			change: function( e, ui ){
				// console.log( ui );
				filterItems();
			},
			
		});
		
		var memoryFormat = function( value ){
			var level = Math.floor( Math.log10( parseInt( value ) ) );
			
			if( level < 3 ){
				return value + "MB";
			}
			else{
				return Math.floor( value / 1000 ) + "GB";
			}
			
		};
		
		var filterItems = function(){
			var postData = {
				name: search.val(),
				diagonal: {
					min: diagonal.slider( "values", 0 ),
					max: diagonal.slider( "values", 1 ),
				},
				memory: {
					min: function(){
						var val = ram.slider( "values", 0 );
						var lvl = Math.floor( Math.log10( val ) );
						if( lvl >= 3 ){
							return Math.floor( val / Math.pow( 10, 3 ) ) * Math.pow( 10, 3 );
						}
						else{
							return val;
						}
					},
					max: function(){
						var val = ram.slider( "values", 1 );
						var lvl = Math.floor( Math.log10( val ) );
						if( lvl >= 3 ){
							return Math.floor( val / Math.pow( 10, 3 ) ) * Math.pow( 10, 3 );
						}
						else{
							return val;
						}
					},
				},
				battery: {
					min: battery.slider( "values", 0 ),
					max: battery.slider( "values", 1 ),
				},
				resolution: [],
				
			};
			
			if( $('#side .row.resolution input:checked').length > 0 ){
				$('#side .row.resolution input:checked').each( function(){
					postData.resolution.push( $(this).val() );
					
				} );
				
			}
			
			// console.log( postData );
			
			$.post(
				'?filter',
				postData,
				function( data ){
					// console.log( data );
					// console.log( JSON.parse( data ) );
					$('#home .view .body').triggerHandler( 'load', [ JSON.parse( data ) ] );
					
				}
			);
			
		}
		
		ram
		.slider({
			range: true,
			min: 0,
			max: parseInt( ranges.memory.max ),
			values: [ 0, parseInt( ranges.memory.max ) ],
			step: 1000,
			create: function( e, ui ){
				ram_text.text( memoryFormat( 0 ) + " - " + memoryFormat( ranges.memory.max ) );
			},
			slide: function( e, ui ){
				ram_text.text( memoryFormat( ui.values[0] ) + " - " + memoryFormat( ui.values[1] ) );
			},
			
		});
		
		diagonal
		.slider({
			range: true,
			min: 0,
			max: parseInt( ranges.diagonal.max ),
			values: [ 0, parseInt( ranges.diagonal.max ) ],
			step: 1,
			create: function( e, ui ){
				diagonal_text.text( 0 + "'' - " + parseInt( ranges.diagonal.max ) + "''" );
			},
			slide: function( e, ui ){
				diagonal_text.text( ui.values[0] + "'' - " + ui.values[1] + "''" );
			},
			
		});
		
		battery
		.slider({
			range: true,
			min: 0,
			max: parseInt( ranges.battery.max ),
			values: [ 0, parseInt( ranges.battery.max ) ],
			step: 200,
			create: function( e, ui ){
				battery_text.text( 0 + "mAh - " + ranges.battery.max + "mAh" );
			},
			slide: function( e, ui ){
				battery_text.text( ui.values[0] + "mAh - " + ui.values[1] + "mAh" );
			},
			
		});
		
		$( ram )
		.add( diagonal )
		.add( battery )
		.on({
			slidechange: function( e, ui ){
				filterItems();
				
			},
			
		});
		
		resolution.change( function( e ){
			filterItems();
			
		} );
		
		filterItems();
		
	})
	(
		$('#side .scroller'),
		$('#side .row.model input'),
		$('#side .row.ram .slider'),
		$('#side .row.ram .text'),
		$('#side .row.diagonal .slider'),
		$('#side .row.diagonal .text'),
		$('#side .row.battery .slider'),
		$('#side .row.battery .text'),
		$('#side .row.resolution input')
	);
	
	/* grid toggle */
	(function( switcher, body ){
		switcher.click( function( e ){
			if( $(this).hasClass('list') ){
				body.addClass('list');
			}
			else{
				body.removeClass('list');
			}
			
		} );
		
	})
	(
		$('#switch .icon'),
		$('#home .body')
	);
	
} );