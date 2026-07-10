<div class="wrap opulentia-cloner-wrap">
	<h1><?php esc_html_e( 'Opulentia Site Cloner', 'opulentia' ); ?></h1>
	<p class="description"><?php esc_html_e( 'Clone any website\'s design into your Opulentia theme. Enter a URL below to capture, analyze, and apply the design.', 'opulentia' ); ?></p>

	<div id="opulentia-cloner-app">
		<div class="opulentia-cloner-step opulentia-cloner-step-active" id="step-capture">
			<h2><?php esc_html_e( 'Step 1: Enter URL', 'opulentia' ); ?></h2>
			<div class="opulentia-cloner-input-group">
				<input type="url" id="opulentia-cloner-url" class="regular-text" placeholder="https://example.com" value="" />
				<button id="opulentia-cloner-capture-btn" class="button button-primary"><?php esc_html_e( 'Capture Site', 'opulentia' ); ?></button>
			</div>
			<div class="opulentia-cloner-progress" id="opulentia-capture-progress" style="display:none;">
				<span class="spinner is-active"></span>
				<span><?php esc_html_e( 'Capturing site design...', 'opulentia' ); ?></span>
			</div>
		</div>

		<div class="opulentia-cloner-step">
			<h2><?php esc_html_e( 'Alternative: Import Dembrandt JSON', 'opulentia' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Run dembrandt --json-only URL locally, paste the JSON output below:', 'opulentia' ); ?></p>
			<textarea id="opulentia-cloner-dembrandt-json" class="large-text code" rows="10" placeholder='Paste Dembrandt JSON here...'></textarea>
			<p>
				<button id="opulentia-cloner-dembrandt-btn" class="button button-secondary"><?php esc_html_e( 'Import Dembrandt Data', 'opulentia' ); ?></button>
			</p>
			<div class="opulentia-cloner-progress" id="opulentia-dembrandt-progress" style="display:none;">
				<span class="spinner is-active"></span>
				<span><?php esc_html_e( 'Processing Dembrandt data...', 'opulentia' ); ?></span>
			</div>
		</div>

		<div class="opulentia-cloner-step" id="step-review" style="display:none;">
			<h2><?php esc_html_e( 'Step 2: Review Design Tokens', 'opulentia' ); ?></h2>
			<div id="opulentia-cloner-screenshot" class="opulentia-cloner-screenshot"></div>
			<div id="opulentia-cloner-design-md" class="opulentia-cloner-design-md"></div>
			<p>
				<button id="opulentia-cloner-analyze-btn" class="button"><?php esc_html_e( 'Analyze Design', 'opulentia' ); ?></button>
			</p>
			<div class="opulentia-cloner-progress" id="opulentia-analyze-progress" style="display:none;">
				<span class="spinner is-active"></span>
				<span><?php esc_html_e( 'Analyzing design tokens...', 'opulentia' ); ?></span>
			</div>
		</div>

		<div class="opulentia-cloner-step" id="step-apply" style="display:none;">
			<h2><?php esc_html_e( 'Step 3: Apply Design', 'opulentia' ); ?></h2>
			<div id="opulentia-cloner-tokens-preview"></div>
			<p>
				<button id="opulentia-cloner-apply-btn" class="button button-primary"><?php esc_html_e( 'Apply Design', 'opulentia' ); ?></button>
				<button id="opulentia-cloner-preview-btn" class="button"><?php esc_html_e( 'Preview Design', 'opulentia' ); ?></button>
			</p>
			<div class="opulentia-cloner-progress" id="opulentia-apply-progress" style="display:none;">
				<span class="spinner is-active"></span>
				<span><?php esc_html_e( 'Applying design...', 'opulentia' ); ?></span>
			</div>
		</div>

		<div id="opulentia-cloner-result" class="opulentia-cloner-result notice" style="display:none;"></div>
		<div id="opulentia-cloner-error" class="opulentia-cloner-error notice notice-error" style="display:none;"></div>
	</div>
</div>

<style>
.opulentia-cloner-wrap { max-width: 900px; }
.opulentia-cloner-step { background: #fff; border: 1px solid #ddd; padding: 20px; margin: 15px 0; border-radius: 4px; }
.opulentia-cloner-step-active { border-left: 4px solid #b8860b; }
.opulentia-cloner-input-group { display: flex; gap: 10px; align-items: center; }
.opulentia-cloner-input-group input { flex: 1; }
.opulentia-cloner-progress { display: flex; align-items: center; gap: 8px; margin-top: 10px; }
.opulentia-cloner-screenshot img { max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px; }
.opulentia-cloner-design-md { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto; }
.opulentia-cloner-result { margin: 10px 0; padding: 10px 15px; }
.opulentia-cloner-error { margin: 10px 0; padding: 10px 15px; }
</style>

<script>
jQuery( function( $ ) {
	var nonce = opulentiaCloner?.nonce || '';

	$( '#opulentia-cloner-capture-btn' ).on( 'click', function() {
		var url = $( '#opulentia-cloner-url' ).val();
		if ( ! url ) { alert( 'Please enter a URL' ); return; }

		$( '#opulentia-capture-progress' ).show();
		$( '#opulentia-cloner-result' ).hide();
		$( '#opulentia-cloner-error' ).hide();

		$.post( ajaxurl, {
			action: 'opulentia_cloner_capture',
			url: url,
			nonce: nonce
		}, function( response ) {
			$( '#opulentia-capture-progress' ).hide();
			if ( response.success ) {
				if ( response.data.screenshot ) {
					$( '#opulentia-cloner-screenshot' ).html( '<img src="' + response.data.screenshot + '" alt="Screenshot" />' );
				}
				$( '#step-capture' ).removeClass( 'opulentia-cloner-step-active' );
				$( '#step-review' ).show();
				$( '#step-apply' ).show();
				$( '#opulentia-cloner-result' ).addClass( 'notice-success' ).html( '<p>' + response.data.message + '</p>' ).show();
			} else {
				$( '#opulentia-cloner-error' ).html( '<p>' + ( response.data?.message || 'Capture failed.' ) + '</p>' ).show();
			}
		}, 'json' ).fail( function() {
			$( '#opulentia-capture-progress' ).hide();
			$( '#opulentia-cloner-error' ).html( '<p>Network error. Please try again.</p>' ).show();
		});
	});

	$( '#opulentia-cloner-analyze-btn' ).on( 'click', function() {
		$( '#opulentia-analyze-progress' ).show();
		$( '#opulentia-cloner-result' ).hide();
		$( '#opulentia-cloner-error' ).hide();

		$.post( ajaxurl, {
			action: 'opulentia_cloner_analyze',
			nonce: nonce
		}, function( response ) {
			$( '#opulentia-analyze-progress' ).hide();
			if ( response.success ) {
				$( '#opulentia-cloner-design-md' ).text( response.data.design_md );
				$( '#opulentia-cloner-tokens-preview' ).html( '<pre>' + JSON.stringify( response.data.analysis, null, 2 ) + '</pre>' );
				$( '#opulentia-cloner-result' ).addClass( 'notice-success' ).html( '<p>' + response.data.message + '</p>' ).show();
			} else {
				$( '#opulentia-cloner-error' ).html( '<p>' + ( response.data?.message || 'Analysis failed.' ) + '</p>' ).show();
			}
		}, 'json' ).fail( function() {
			$( '#opulentia-analyze-progress' ).hide();
			$( '#opulentia-cloner-error' ).html( '<p>Network error. Please try again.</p>' ).show();
		});
	});

	$( '#opulentia-cloner-apply-btn' ).on( 'click', function() {
		if ( ! confirm( 'This will update your theme settings. Continue?' ) ) { return; }
		$( '#opulentia-apply-progress' ).show();
		$( '#opulentia-cloner-result' ).hide();
		$( '#opulentia-cloner-error' ).hide();

		$.post( ajaxurl, {
			action: 'opulentia_cloner_apply',
			nonce: nonce
		}, function( response ) {
			$( '#opulentia-apply-progress' ).hide();
			if ( response.success ) {
				$( '#opulentia-cloner-result' ).addClass( 'notice-success' ).html( '<p>' + response.data.message + '</p>' ).show();
			} else {
				$( '#opulentia-cloner-error' ).html( '<p>' + ( response.data?.message || 'Apply failed.' ) + '</p>' ).show();
			}
		}, 'json' ).fail( function() {
			$( '#opulentia-apply-progress' ).hide();
			$( '#opulentia-cloner-error' ).html( '<p>Network error. Please try again.</p>' ).show();
		});
	});

	$( '#opulentia-cloner-dembrandt-btn' ).on( 'click', function() {
		var json = $( '#opulentia-cloner-dembrandt-json' ).val().trim();
		if ( ! json ) { alert( 'Please paste Dembrandt JSON data first.' ); return; }

		$( '#opulentia-dembrandt-progress' ).show();
		$( '#opulentia-cloner-result' ).hide();
		$( '#opulentia-cloner-error' ).hide();

		$.post( ajaxurl, {
			action: 'opulentia_cloner_dembrandt',
			dembrandt_json: json,
			nonce: nonce
		}, function( response ) {
			$( '#opulentia-dembrandt-progress' ).hide();
			if ( response.success ) {
				$( '#opulentia-cloner-design-md' ).text( response.data.design_md );
				$( '#opulentia-cloner-tokens-preview' ).html( '<pre>' + JSON.stringify( response.data.theme_mods, null, 2 ) + '</pre>' );
				$( '#step-review' ).show();
				$( '#step-apply' ).show();
				$( '#opulentia-cloner-result' ).addClass( 'notice-success' ).html( '<p>' + response.data.message + '</p>' ).show();
			} else {
				$( '#opulentia-cloner-error' ).html( '<p>' + ( response.data?.message || 'Import failed.' ) + '</p>' ).show();
			}
		}, 'json' ).fail( function() {
			$( '#opulentia-dembrandt-progress' ).hide();
			$( '#opulentia-cloner-error' ).html( '<p>Network error. Please try again.</p>' ).show();
		});
	});

	$( '#opulentia-cloner-preview-btn' ).on( 'click', function() {
		$.post( ajaxurl, {
			action: 'opulentia_cloner_preview',
			nonce: nonce
		}, function( response ) {
			if ( response.success && response.data.preview_url ) {
				window.open( response.data.preview_url, '_blank' );
			}
		}, 'json' );
	});
});
</script>
