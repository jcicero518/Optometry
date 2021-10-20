<!-- General -->
<div class="panel general-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'General', 'wp-simple-seo' ); ?></h3>

        <div class="option">
			<label for="general_enabled">
				<div class="left">
					<strong><?php _e( 'Enable Social Metadata?', 'wp-simple-seo' ); ?></strong>
				</div>
				<div class="right">
					<select name="general[enabled]" id="general_enabled" size="1" data-conditional="<?php echo $conditional_tabs; ?>">
						<option value="1"<?php selected( $this->get_setting( 'social', 'general[enabled]' ), 1 ); ?>><?php _e( 'Yes', 'wp-simple-seo' ); ?></option>
						<option value="0"<?php selected( $this->get_setting( 'social', 'general[enabled]' ), 0 ); ?>><?php _e( 'No', 'wp-simple-seo' ); ?></option>
					</select>
					
					<p class="description">
						<?php _e( 'If enabled, outputs metadata for use when your Posts, Pages, Custom Post Types etc. are shared on social networks. Also outputs metadata for Google\'s Knowledge Graph.', 'wp-simple-seo' ); ?>
					</p>
				</div>
			</label>
		</div>
    </div>
</div>

<!-- Profiles -->
<div class="panel profiles-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Profiles', 'wp-simple-seo' ); ?></h3>

        <div class="option">
	        <p class="description">
	        	<?php _e( 'Define your social media profile links here.  These will be used both when your content is shared by users on social networks, and by Google\'s Knowledge Graph', 'wp-simple-seo' ); ?>
	        </p>
        </div>

        <div class="option">
			<div class="left">
				<strong><?php _e( 'Facebook Page URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="facebook[url]" value="<?php echo $this->get_setting( 'social', 'facebook[url]' ); ?>" class="widefat" />
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Twitter Profile URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="text" name="twitter[url]" value="<?php echo $this->get_setting( 'social', 'twitter[url]' ); ?>" class="widefat" />
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Google+ URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="google[url]" value="<?php echo $this->get_setting( 'social', 'google[url]' ); ?>" class="widefat" />
			</div>
		</div>
		
	    <div class="option">
			<div class="left">
				<strong><?php _e( 'Instagram URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="instagram[url]" value="<?php echo $this->get_setting( 'social', 'instagram[url]' ); ?>" class="widefat" />
			</div>
		</div>
		
	    <div class="option">
			<div class="left">
				<strong><?php _e( 'YouTube URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="youtube[url]" value="<?php echo $this->get_setting( 'social', 'youtube[url]' ); ?>" class="widefat" />
			</div>
		</div>

	    <div class="option">
			<div class="left">
				<strong><?php _e( 'LinkedIn URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="linkedin[url]" value="<?php echo $this->get_setting( 'social', 'linkedin[url]' ); ?>" class="widefat" />
			</div>
		</div>
		
	    <div class="option">
			<div class="left">
				<strong><?php _e( 'MySpace URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="myspace[url]" value="<?php echo $this->get_setting( 'social', 'myspace[url]' ); ?>" class="widefat" />
			</div>
		</div>
		
	    <div class="option">
			<div class="left">
				<strong><?php _e( 'Pinterest URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="pinterest[url]" value="<?php echo $this->get_setting( 'social', 'pinterest[url]' ); ?>" class="widefat" />
			</div>
		</div>
		
	    <div class="option">
			<div class="left">
				<strong><?php _e( 'SoundCloud URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="soundcloud[url]" value="<?php echo $this->get_setting( 'social', 'soundcloud[url]' ); ?>" class="widefat" />
			</div>
		</div>
		
	    <div class="option">
			<div class="left">
				<strong><?php _e( 'Tumblr URL', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="url" name="tumblr[url]" value="<?php echo $this->get_setting( 'social', 'tumblr[url]' ); ?>" class="widefat" />
			</div>
		</div>
    </div>
</div>

<!-- Open Graph -->
<div class="panel open-graph-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Open Graph', 'wp-simple-seo' ); ?></h3>

        <div class="option">
	        <p class="description">
	        	<?php _e( 'Define your Open Graph metadata here.  This is used by Facebook, Pinterest and some smaller social networks.', 'wp-simple-seo' ); ?>
	        </p>
        </div>

        <div class="option">
			<div class="left">
				<strong><?php _e( 'Default Image', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<?php
                $default_image = $this->get_setting( 'social', 'open_graph[default_image]' );
                ?> 
                                        
                <span class="wp-media-buttons">
                    <a href="#" class="button insert-media-plugin add_media" data-input="social_open_graph_default_image_input" data-output="social_open_graph_default_image_output">
                        <span class="wp-media-buttons-icon"></span>
                        <?php _e( 'Change Default Image', 'wp-simple-seo' ); ?>
                    </a>
                    <a href="#" class="button button-red delete-media-plugin delete_media" data-input="social_open_graph_default_image_input" data-output="social_open_graph_default_image_output">
                        <?php _e( 'Remove Default Image', 'wp-simple-seo' ); ?>
                    </a>
                </span>
			</div>
			<div class="left">
				&nbsp;
			</div>
			<div class="right">
				<input type="hidden" id="social_open_graph_default_image_input" name="open_graph[default_image]" value="<?php echo $default_image; ?>" />
                <?php
                // Output an image if an image ID exists
                if ( ! empty( $default_image ) ) {
                    $default_image_src = wp_get_attachment_image_src( $default_image, 'thumbnail' );
                    $default_image_url = ( ! $default_image_src ? '' : $default_image_src[0] );
                    ?>
                    <img src="<?php echo $default_image_url; ?>" id="social_open_graph_default_image_output" />
                    <?php
                } else {
                    ?>
                    <img src="" id="social_open_graph_default_image_output" />
                    <?php   
                }
                ?>
			</div>
		</div>
    </div>
</div>

<!-- Twitter -->
<div class="panel twitter-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Twitter', 'wp-simple-seo' ); ?></h3>

        <div class="option">
			<div class="left">
				<strong><?php _e( 'Card Type', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<select name="twitter[card_type]" size="1">
					<?php
					foreach ( WP_Simple_SEO_Common::get_instance()->get_twitter_card_types() as $card_type ) {
						?>
						<option value="<?php echo $card_type['name']; ?>"<?php selected( $this->get_setting( 'social', 'twitter[card_type]' ), $card_type['name'] ); ?>><?php echo $card_type['label']; ?></option>
						<?php
					}
					?>
				</select>
				<p class="description">
					<?php _e( 'The card type determines the preview / media you want to display when your site\'s URL is shared on Twitter.', 'wp-simple-seo' ); ?>
				</p>
			</div>
		</div>

		<div class="option">
			<div class="left">
				<strong><?php _e( 'Twitter Username', 'wp-simple-seo' ); ?></strong>
			</div>
			<div class="right">
				<input type="text" name="twitter[username]" value="<?php echo $this->get_setting( 'social', 'twitter[username]' ); ?>" class="widefat" />
			</div>
		</div>
    </div>
</div>

<!-- Save -->
<div class="submit">
    <input type="submit" name="submit" value="<?php _e( 'Save', 'wp-simple-seo' ); ?>" class="button button-primary" />
</div>