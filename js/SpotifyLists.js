SpotifyList = Class.create({

	currentTrack: 0,
	totalTracks: 0,
	usableTracks: 0,
	playlistID: null,
	clipboard: null,
	initiated: false,

	initialize: function(playlistID) {
		
		this.playlistID = playlistID;
				
		document.observe('track:finished', this.getNextPlaylistItem.bindAsEventListener(this));
		$('ivystart').observe('click', this.initIvy.bindAsEventListener(this));
		
		this.clipboard = new ZeroClipboard.Client();
        this.clipboard.setText(''); // will be set later on mousedown
        this.clipboard.setHandCursor(true);
        
        this.clipboard.addEventListener("mouseDown", this.copyToClipboard.bind(this) );
        this.clipboard.addEventListener("complete", this.copiedToClipboard.bind(this) );
        
		
		this.getPlaylistCount();
		//this.getNextPlaylistItem();
		
	},
	
	initIvy: function()
	{
		if(this.initiated)
			return;
		
		$('playlistoverview').hide();
		$('ivyprogress').show();
		Form.Element.disable('ivystart');
		
		this.getNextPlaylistItem();
	},
	
	getPlaylistCount: function() {
		var response = new Ajax.Request('ivy/ajax_get_playlist_count/playlist/'+this.playlistID, 
		{ method: 'get', onSuccess: this.setTotalTracks.bindAsEventListener(this) } );		
	},
	
	setTotalTracks: function(event) {
		this.totalTracks = event.headerJSON.total_tracks;
		
		$$('.totaltracks').each(function(element) { element.update(this.totalTracks); }, this);
	},
	
	
	getNextPlaylistItem: function() {
			
		this.currentTrack++;
		
		var item = new Ajax.Request('ivy/ajax_get_next/playlist/'+this.playlistID+'/track/'+this.currentTrack, 
		{ method: 'get', onSuccess: this.showNewPlaylistItem.bindAsEventListener(this), onFailure: function() {  } } );
	},
	
	
	showNewPlaylistItem: function(event) {
				
		
		if(event.getHeader('Content-Type') == 'text/x-json')
			var response = event.responseText.evalJSON();
				
		var tracks = response.response;
		var trackdata = response.trackdata;
		
		
		if(response.code)
		{
			//console.log(response.code);
			switch(response.code)
			{
				case "MISSING_ARGUMENTS":
				////console.log('not all arguments');
				this.disableTable('Not all arguments needed for AJAX request were present.');
				break;
				
				case 'END_OF_PLAYLIST':
				this.wrapUp();
				break;
				
				case 'NO_TRACKS_FOUND':
				this.disableTable('No tracks found (at all) for track '+this.currentTrack+'...', "red", trackdata);
				document.fire('track:finished');
				break;
				
				case 'NO_SUITABLE_TRACKS':
				this.disableTable('No suitable tracks found for track '+this.currentTrack+'...', "red", trackdata);
				document.fire('track:finished');
				break;								
				
				case 'UNKNOWN_ERROR':
				this.disableTable('An unknown error occured. Trying next track...');
				document.fire('track:finished');
				break;
			}
			
		}
		else
		{
			//console.log("track OK");
			this.usableTracks++;				
			this.populate(tracks[0], trackdata);
		}
	}, 
	
	populate: function(track, trackdata) {
	
		//console.log(this.currentTrack);
		$('currenttrack').update(this.currentTrack);
		$('tracklist').insert(track["@attributes"].href+"\n");
						
		document.fire('track:finished');
		//console.log('track complete');
		//window.location.href = track["@attributes"].href;
	},
	
	disableTable: function(message, mood, trackdata) {
/*
		if(!mood)
			mood = 'red';
		$('resulttable').hide();
		
		if(mood != "green")
			$('ctrack').update(this.currentTrack);
		
		if(trackdata)
		{
			$('query').update('Query: '+trackdata["artist"]+" - "+trackdata["title"]);
		}
		else
		{
			$('query').update('');
		}
*/

		$('currenttrack').update(this.currentTrack);
		
	},
	
	enableTable: function() {
		$('message').hide();
		$('resulttable').show();
	},
	
	writeRawInfo: function(track, trackdata) {
		////console.log('Query: '+trackdata["artist"]+" - "+trackdata["title"]);
		////console.log(track);
	},
	
	wrapUp: function() {
		//console.log("it's a wrap");
		//this.disableTable("That's all folks! "+this.usableTracks+" out of "+this.totalTracks+" tracks have been found on Spotify.", "green");
		
		$('ivyprogress').hide();
		$('headerstep2').hide();
		
		$$('.usabletracks').each(function(element) { element.update(this.usableTracks); }, this);
		
		$('ivydone').show();
		$('headerstep3').show();
		$('backbutton').setStyle({ visibility: "visible" });
		
		$('step-2-active').writeAttribute('id', 'step-2');
		$('step-3').writeAttribute('id', 'step-3-active');

		$('tracklist').value.trim(); //trim last \n from result list as this confuses Spotify clients
		
		
/*
		$('copy_btn').show();
		$('copyinput').insert($F('tracklist'));
		$('copyinput').setOpacity(0);
*/
				
		this.clipboard.glue('copyimage');
		this.clipboard.show();
		
		//write the usable tracks count to database for analysis
		this.postUsableTracks();

	},
	
	postUsableTracks: function()
	{
		var postRequest = new Ajax.Request('ivy/ajax_post_usabletracks', 
		{ method: 'post', parameters: { playlist_ID: this.playlistID, usableTracks: this.usableTracks }, onSuccess: function(transport) {
													if(transport.getHeader('Content-Type') == 'text/x-json')
														var response = transport.responseText.evalJSON();
													
													console.log(response);
														
  												}, onFailure: function() {  } } );
	},
	
	selectTrackslist: function() {
				
		if (document.selection) {
		var range = document.body.createTextRange();
 	        range.moveToElementText($('copyinput'));
		range.select();
		}
		
		else if (window.getSelection) {
		var range = document.createRange();
		range.selectNode($('copyinput'));
		window.getSelection().addRange(range);
		}
		
		//$('copyinput').select();
	},
	
	deselectTrackslist: function() {
		if(document.selection)
		{
			document.selection.empty();
		}
		else if(window.getSelection)
        {
        	window.getSelection().removeAllRanges();
        }
	},
	
	copyToClipboard: function(client)
	{
		this.clipboard.setText($F('tracklist'));				
	},
			
	copiedToClipboard: function(client, text)
	{
		//console.log("text "+text+" copied.");
		$("clipboardmessage").update("The playlist is copied to your clipboard!");
	}

});