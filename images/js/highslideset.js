	hs.graphicsDir = '/images/js/graphics/';
	hs.outlineType = 'rounded-white';
	hs.numberOfImagesToPreload = 0;
	hs.showCredits = false;
	hs.dimmingOpacity = 0.60;
	hs.lang = {
		loadingText :     'Loading...',
		playTitle :       '',
		pauseTitle:       '',
		previousTitle :   '',
		nextTitle :       '',
		moveTitle :       '',
		closeTitle :      'Close (Esc)',
		fullExpandTitle : '',
		restoreTitle :    '',
		focusTitle :      'Focus',
		loadingTitle :    ''
	};
	
	hs.align = 'center';
	hs.transitions = ['expand', 'crossfade'];
	hs.addSlideshow({
		interval: 4000,
		repeat: false,
		useControls: true,
		fixedControls: 'fit',
		overlayOptions: {
			opacity: .75,
			position: 'bottom center',
			hideOnMouseOut: true
		}
	});