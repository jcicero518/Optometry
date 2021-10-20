<!-- General -->
<div class="panel general-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'General', 'wp-simple-seo' ); ?></h3>

        <div class="option">
			<label for="general_enabled">
				<div class="left">
					<strong><?php _e( 'Enable XML Sitemap?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<select name="general[enabled]" id="general_enabled" size="1" data-conditional="wp-simple-seo-sitemap-url,wp-simple-seo-sitemap-options">
						<option value="1"<?php selected( $this->get_setting( 'sitemap', 'general[enabled]' ), 1 ); ?>><?php _e( 'Yes', 'wp-simple-seo' ); ?></option>
						<option value="0"<?php selected( $this->get_setting( 'sitemap', 'general[enabled]' ), 0 ); ?>><?php _e( 'No', 'wp-simple-seo' ); ?></option>
					</select>

					<span class="description">
						<?php _e( 'If enabled, generates an XML sitemap which can be submitted to the search engines', 'wp-simple-seo' ); ?>
					</span>
					<p class="description" id="wp-simple-seo-sitemap-url">
						<a href="<?php bloginfo( 'url' ); ?>/sitemap_index.xml" target="_blank" class="button">
							<?php _e( 'View XML Sitemap', 'wp-simple-seo' ); ?>
						</a>
					</p>
				</div>
			</label>
		</div>

		<div id="wp-simple-seo-sitemap-options">
			<!-- Google -->
			<div class="option">
				<div class="left">
					<strong><?php _e( 'Submit to Google?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<?php
					// Check if WP Simple SEO has access to Google
    				if ( ! $screen['data']['google']['oauth_authorized'] ) {
    					?>
    					<a href="<?php echo $screen['data']['google']['oauth_url']; ?>" class="button">
	                        <?php _e( 'Connect WP Simple SEO to Google', 'wp-simple-seo' ); ?>
	                    </a>
	                    <p class="description">
	                        <?php _e( 'Connecting WP Simple SEO to your Google account allows us to submit your web site, verify ownership and submit your XML sitemap to Google.  Google will then be able to crawl / index your site, making it possible for your site to appear on Google\'s search results.', 'wp-simple-seo' ); ?>
	                    </p>
	                    <?php
	                } else {
	                	// If sitemap exists in Google Search Console, display a message
						// Otherwise give the option to oAuth with Google and/or submit sitemap now
						if ( $screen['data']['google']['sitemap_submitted'] ) {
							?>
							<p class="description">
								<?php _e( 'Your XML Sitemap has already been submitted to Google.', 'wp-simple-seo' ); ?>
							</p>
							<?php
						} else {
							// If no Google Access Token exists, set the button URL to run the oAuth process
							if ( ! $screen['data']['google']['oauth_authorized'] ) {
								?>
								<br />
								<a href="<?php echo $screen['data']['google']['oauth_url']; ?>" title="<?php _e( 'Submit Sitemap to Google', 'wp-simple-seo' ); ?>" class="button">
									<?php _e( 'Submit Sitemap to Google', 'wp-simple-seo' ); ?>
								</a>
								<?php
							} else {
								?>
								<br />
								<a href="admin.php?page-wp-simple-seo-sitemap&action=google-sitemap-submit" title="<?php _e( 'Submit Sitemap to Google', 'wp-simple-seo' ); ?>" class="button">
									<?php _e( 'Submit Sitemap to Google', 'wp-simple-seo' ); ?>
								</a>
								<?php
							}
							?>
							<p class="description">
								<?php _e( 'WP Simple SEO can automatically submit your sitemap to Google. Click the Submit Sitemap to Google button below.', 'wp-simple-seo' ); ?>
							</p>
							<?php
						}
	                }
					?>
				</div>
			</div>

			<!-- Bing -->
			<div class="option">
				<div class="left">
					<strong><?php _e( 'Submit to Bing?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<p class="description">
						<?php _e( 'To submit your sitemap to Bing, you need to manually do this.  Click the button below, and then enter the following URL in the "Submit a sitemap" field:', 'wp-simple-seo' ); ?>
					</p>
					<code id="sitemap_url">
						<?php bloginfo( 'url' ); ?>/sitemap_index.xml
					</code>
					<a href="#" class="button dashicons dashicons-clipboard" title="<?php _e( 'Click to copy this URL to your clipboard', 'wp-simple-seo' ); ?>" data-clipboard-target="#sitemap_url"></a>

					<br />
					<a href="https://www.bing.com/webmaster/configure/sitemaps/home?url=<?php echo urlencode( get_bloginfo( 'url' ) ); ?>" title="<?php _e( 'Submit Sitemap to Bing', 'wp-simple-seo' ); ?>" target="_blank" class="button">
						<?php _e( 'Submit Sitemap to Bing', 'wp-simple-seo' ); ?>
					</a>
				</div>
			</div>
		</div>
    </div>
</div>

<!-- Save -->
<div class="submit">
    <input type="submit" name="submit" value="<?php _e( 'Save', 'wp-simple-seo' ); ?>" class="button button-primary" />
</div>