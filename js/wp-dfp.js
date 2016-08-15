(function($, window) {

	/**
	 * Refers to the last known width of the viewport
	 * @since 1.1.6
	 * @var int
	 */
	wpdfp.winWidth = null;

	/**
	 * Refers to whether or not wpdfp has been initialized already.
	 * @since 1.1.6
	 * @var bool
	 */
	wpdfp.didInit = false;

	wpdfp.init = function() {
		console.log("wpdfp.init");
		var sizeMapping = {}, $ads = $('.wp-dfp-ad-unit');

		// If a network code is not set, bail and attempt to display an error message
		if (!wpdfp.network.length) {
			if (wpdfp.messages.noNetworkCode) {
				// Display the error message in place of each in-page ad unit
				$ads.not('[data-outofpage]').replaceWith(wpdfp.messages.noNetworkCode);
			}

			return;
		}

		// Calculate size mappings for each ad space
		$.each(wpdfp.slots, function(name) {
			console.log("wpdfp.slots each");
			$ads.filter(function() {
				console.log("ads filter looking for: " + name);
				var regex = new RegExp(name)
				  , $this = $(this);

				return $this.data('adunit') && $this.data('adunit').match(name);
			}).each(function(index) {
				var $this      = $(this)
			  	  , id         = $this.data('adunit')
			  	  , $container = $this.closest('.wp-dfp-ad-slot')
			  	  , rules      = wpdfp.slots[id]
			  	  , adSizes    = null
					  , currRule   = $this.data( 'wpdfp.sizerule' ) || null
					  , newRule    = null
					  , maxWidth   = 0
					  , isLoaded   = $this.data( 'wpdfp.isloaded' ) || null
				;

				console.log("each filter ad unit: " + id + ", rules: " + JSON.stringify(rules) + ", currRule: " + currRule + ", newRule: " + newRule + ", isLoaded: " + isLoaded);

				// Using the defined sizing rules for this ad slot, determine which set
				// of ad sizes should be used.
				if (rules != 'oop') {
					$.each(rules, function(width, sizes) {
						width = parseInt(width);
						console.log("container width: " + $container.width() + ", width: " + width + ", maxWidth: " + maxWidth);
						if ($container.width() >= width && width > maxWidth) {
							maxWidth = width;
							adSizes = rules[width];
							newRule = width;
					  	}
					});

					console.log("newRule: " + newRule);
					// If the ad sizing rule hasn't changed for this ad unit
					// then remove it from the $ads object and move on. This
					// fixes an issue with ads being reloaded when nothing
					// has changed.
					if ( currRule === newRule && isLoaded ) {
						$ads.splice( $.inArray(this, $ads), 1 );
						return;
					}

					$this.data( 'wpdfp.sizerule', newRule );

					if (adSizes) {
					  	sizeMapping[id] = [
					  		{ browser: [0, 0], ad_sizes: adSizes }
					  	];
					}
					else {
						sizeMapping[id] = [
							{ browser: [0, 0], ad_sizes: [] }
						];
					}
				}
			});
		});

		if ( $.fn.dfp && $ads.length && !$.isEmptyObject( sizeMapping ) ) {
				console.log("ads before calling dfp!");

			$ads.dfp({
				dfpID:               wpdfp.network,
				collapseEmptyDivs:   true,
				setUrlTargeting:     false,
				setTargeting:        wpdfp.targeting,
				sizeMapping:         sizeMapping,
				beforeEachAdLoaded:  wpdfp.beforeEachAdLoaded,
				afterEachAdLoaded:   wpdfp.afterEachAdLoaded,
				enableSingleRequest: false,
				setCentering:        true,
				refreshExisting:     true
			});
		}
	};

	wpdfp.beforeEachAdLoaded = function($adUnit) {
		console.log("beforeEachAdLoaded called");
		$adUnit.data( 'wpdfp.isloaded', false );
	}

	wpdfp.afterEachAdLoaded = function($adUnit, event) {
		console.log("afterEachAdLoaded called");
		if (!event.isEmpty) {
			console.log("ad isn't empty");
			$adUnit.data( 'wpdfp.isloaded', true );
			//attempt to center ad
			$adUnit.css({'max-width': event.size[0] + 'px', 'margin': '0 auto'});
			$adUnit.show();
		}
		else {
			console.log("ad is empty");
			$adUnit.data( 'wpdfp.isloaded', false );
			//$adUnit.hide();
		}
	};

	var debounce = function(func, threshold, execAsap) {
		var timeout;
		return function debounced () {
			var obj = this, args = arguments;
			function delayed () {
				if (!execAsap)
					func.apply(obj, args);
				timeout = null;
			}
			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);
			timeout = setTimeout(delayed, threshold || interval);
		};
	};

	/**
	 * Respond to window resize events.
	 * @since 1.1.6
	 */
	wpdfp.onResize = debounce(function() {
		var $win = $(window);

		// Ensure that the window size has actually changed. Some
		// mobile devices trigger a resize event when scrolling.
		if (wpdfp.winWidth !== $win.width()) {
			wpdfp.winWidth = $win.width();
			console.log("wpdfp.onResize");
			wpdfp.init();
		}
	},500);

	$(window).on('load', function () {
		wpdfp.init();
		wpdfp.winWidth = $(window).width();
		// Only wire up the resize handler after loading is complete to prevent fire of resize before page is loaded.
		$(window).on('resize', wpdfp.onResize);
	});

})(window.jQuery, window);

