IvyControls = Class.create({

	currentSelectedElement: null,
	tabElements: null,
	currentFieldset: null,
	
	
	initialize: function()
	{
		//detect current selected tab
		$$('.tabs a.selected').each(this.setInitialTab, this);
		
		//search for all tab elements there are
		$$('.tabs a').each(function(element) { element.observe('click', this.changeCurrentTab.bindAsEventListener(this, element)); }, this);
		
		//search for a fake file input and then put an observer on it
		if($("realfilename") && $("fakefilename"))
		{
			$("realfilename").observe("change", this.copyFilename.bindAsEventListener(this));
			this.defaultFakeValue = $F("fakefilename");
		}
	},
	
	setInitialTab: function(element)
	{
		this.currentSelectedElement = element;
		$('uploadtype').setValue(element.identify());
		
		var fieldset = element.getAttribute("rel");
		$(fieldset).show();
		this.currentFieldset = fieldset;
	},
	
	changeCurrentTab: function(event, element)
	{
		if(element.hasClassName("selected"))
			return;
		
		element.addClassName("selected");
		$('uploadtype').setValue(element.identify());
		this.currentSelectedElement.removeClassName("selected");
		this.currentSelectedElement = element;
		
		var fieldset = element.getAttribute("rel");
		
		if(fieldset != this.currentFieldset)
		{
			$(this.currentFieldset).hide();
			$(fieldset).show();
			this.currentFieldset = fieldset;
		}
	},
	
	copyFilename: function(event)
	{
		if($F("realfilename") == "")
			$("fakefilename").setValue(this.defaultFakeValue);
		else
			$("fakefilename").setValue($F("realfilename").replace("C:\\fakepath\\", ''));
			
			//in HTML5 there is a security issue that doesn't let JS know the local path of a file so it adds fakepath to it.
			//That's fine, but we don't want to show that in our input field. So we strip it.
	}

});