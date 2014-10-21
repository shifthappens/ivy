<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html> 
	<head> 
	<style type="text/css"> 
 
	body {
		font-family: Helvetica, Verdana, Arial, sans-serif;
	}
	
	#result {
		width: 750px;
	}
	
	#tracklistdiv, #resulttable {
		float: left;
		width: 375px;
	}
	</style> 
		<title>Spotify Playlist creator</title> 
		<script src="http://labs/spotifylists/trunk/js/ZeroClipboard.js" type="text/javascript"></script>		
	<base href="http://labs/spotifylists/" />	
	</head> 
	<body>
		Copy to Clipboard: <input type="text" id="clip_text" size="40" value="Copy me!"/><br/><br/> 
        
        <div id="d_clip_button">Copy To Clipboard</div>

		<script>       
        ZeroClipboard.setMoviePath("<?=base_url() ?>resources/ZeroClipboard.swf");

        var clip = new ZeroClipboard.Client();        
        
        clip.setText( '' ); // will be set later on mouseDown
        clip.setHandCursor( true );
        clip.setCSSEffects( true );
        
        clip.addEventListener( 'load', function(client) {
                // alert( "movie is loaded" );
        } );
        
        clip.addEventListener( 'complete', function(client, text) {
                alert("Copied text to clipboard: " + text );
        } );
        
        clip.addEventListener( 'mouseOver', function(client) {
                // alert("mouse over"); 
        } );
        
        clip.addEventListener( 'mouseOut', function(client) { 
                // alert("mouse out"); 
        } );
        
        clip.addEventListener( 'mouseDown', function(client) { 
                // set text to copy here
                clip.setText( document.getElementById('clip_text').value );
                
                // alert("mouse down"); 
        } );
        
        clip.addEventListener( 'mouseUp', function(client) { 
                // alert("mouse up"); 
        } );
        
        clip.glue( 'd_clip_button' );
		</script>			
	
	</body>
</html>