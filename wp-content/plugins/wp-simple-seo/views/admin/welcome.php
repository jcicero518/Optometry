<div class="wrap about-wrap">
    <h1>
    	<?php _e( 'Welcome to WP Simple SEO', 'wp-simple-seo' ); ?>
    </h1>

    <?php
    // Notices
    foreach ( $this->notices as $type => $notices_type ) {
    	if ( count( $notices_type ) == 0 ) {
    		continue;
    	}
    	?>
    	<div class="<?php echo ( ( $type == 'success' ) ? 'updated' : $type ); ?> notice">
	    	<?php
	    	foreach ( $notices_type as $notice ) {
	    		?>
	    		<p><?php echo $notice; ?></p>
	    		<?php
	    	}
	    	?>
	    </div>
	    <?php
    }
    ?>
		
	<form action="admin.php?page=<?php echo $this->base->plugin->name; ?>&finished_setup=1" method="post">	
		<?php
		if ( $screen['data']['no_options_displayed'] ) {
			?>
			<div class="about-text">
				<?php _e( 'Thanks for choosing WP Simple SEO.  There aren\'t any options to display (such as submitting your XML sitemap), because we\'ve detected this is a local or development web site.', 'wp-simple-seo' ); ?>
			</div>
			<?php
		} else {
			?>
			<div class="about-text">
				<?php _e( 'Thanks for choosing WP Simple SEO. Your site is now configured for SEO, but there are a few things you might want to do.', 'wp-simple-seo' ); ?>
			</div>
			<?php

			// Visibility
			if ( $screen['data']['is_production_site'] && ! $screen['data']['is_public'] ) {
				?>
				<label>
					<input type="checkbox" name="visibility_checkbox" value="1" />
					<?php _e( 'Allow Search Engines to access your site.', 'wp-simple-seo' ); ?>
					<p class="description">
						<?php _e( 'Right now, WordPress is configured to block search engines from indexing your site\'s content.  Tick this box to stop WordPress blocking search engines.', 'wp-simple-seo' ); ?>
					</p>
				</label>
				<?php
			}

			// Tagline
			if ( $screen['data']['default_tagline'] ) {
				?>
				<label>
					<input type="checkbox" name="tagline_checkbox" value="1" data-conditional="tagline" />
					<?php _e( 'Change Tagline.', 'wp-simple-seo' ); ?>
					<p class="description no-bottom-margin">
						<?php _e( 'WordPress has a "tagline" option, which is a description of your web site.  Right now, it\'s still set to the default tagline, which isn\'t great for SEO. Tick this box to change the tagline.', 'wp-simple-seo' ); ?>
					</p>

					<input type="text" name="tagline" id="tagline" value="" placeholder="<?php _e( 'Enter a new tagline - this should be a few words describing your site.', 'wp-simple-seo' ); ?>" />
				</label>
				<?php
			}

			// Google
			if ( $screen['data']['is_production_site'] ) {
				?>
				<label>
					<input type="checkbox" name="sitemap_checkbox" value="1" <?php echo ( $screen['data']['google']['oauth_authorized'] ? ' checked disabled="disabled"' : '' ); ?> />
					<?php _e( 'Submit your Site and Sitemap to Google', 'wp-simple-seo' ); ?>
					
					<p class="description">
						<?php
						// If a Google Access Token was returned, the user completed oAuth, so the site and sitemap are registered
						// with the Google Search Console.
						if ( $screen['data']['google']['oauth_authorized'] ) {
							_e( 'Thanks - your site and sitemap has been submitted to Google successfully!', 'wp-simple-seo' );
						} else {
							// Show button to allow the user to submit Sitemap to Google (i.e. run the oAuth process)
							_e( 'WP Simple SEO can automatically add your site to Google, verify your ownership of it, and submit its XML sitemap.  This tells Google which of your web site\'s URLs it should attempt to index and rank.', 'wp-simple-seo' ); 
							?>
							<br />
							<a href="<?php echo $screen['data']['google']['oauth_url']; ?>" class="button" title="<?php _e( 'Submit Sitemap to Google', 'wp-simple-seo' ); ?>">
								<?php _e( 'Submit Site &amp; Sitemap to Google', 'wp-simple-seo' ); ?>
							</a>
							<?php
						}
						?>
					</p>
				</label>

				<?php
			}

			// Import from another SEO Plugin
			if ( count( $screen['data']['import_sources'] ) > 0 ) {
				?>
				<label>
					<input type="checkbox" name="import_checkbox" value="1" data-conditional="import_source" />
					<?php _e( 'Import SEO Configuration from another Plugin?', 'wp-simple-seo' ); ?>
					<p class="description">
						<?php _e( 'We\'ve detected that another SEO plugin is, or was, used on this site.  You can optionally choose to import its configuration settings now.', 'wp-simple-seo' ); ?>
					</p>

					<p class="description">
						<select id="import_source" name="import_source" size="1">
							<?php
							foreach ( (array) $screen['data']['import_sources'] as $import_source => $label ) {
								?>
								<option value="<?php echo $import_source; ?>"><?php echo $label; ?></option>
								<?php
							}
							?>
						</select>
					</p>
				</label>
				<?php
			}
		} // no_options_displayed
		?>

		<div class="submit">
			<?php
			wp_nonce_field( $this->base->plugin->name . '_welcome', $this->base->plugin->name . '_nonce' ); 
			?>
			<input type="submit" name="submit" value="<?php _e( 'Finish Setup', 'wp-simple-seo' ); ?>" class="button button-primary" />
		</div>
	</form>
</div>