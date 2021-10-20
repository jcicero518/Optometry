<!-- General -->
<div class="panel google-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Register and Verify Site', 'wp-simple-seo' ); ?></h3>

        <div class="option">
            <p class="description">
                <?php _e( 'Connecting WP Simple SEO to your Google account allows us to submit your web site, verify ownership and submit your XML sitemap to Google.  Google will then be able to crawl / index your site, making it possible for your site to appear on Google\'s search results.', 'wp-simple-seo' ); ?>
            </p>
        </div>
        
        <?php
        // Check if WP Simple SEO has access to Google
        if ( $screen['data']['google']['oauth_authorized'] ) {
            // Connected to Google
            ?>
            <div class="option">
                <div class="left">
                    <strong><?php _e( 'Site Registered', 'wp-simple-seo' ); ?></strong>
                </div>
                <div class="right">
                    <?php
                    if ( $screen['data']['google']['site_registered'] ) {
                        ?>
                        <strong class="success"><?php _e( 'Yes', 'wp-simple-seo' ); ?></strong>
                        <?php
                    } else {
                        ?>
                        <strong class="error"><?php _e( 'No', 'wp-simple-seo' ); ?></strong>
                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wp-simple-seo&action=google_site_register' ), $this->base->plugin->name . '_' . $screen['name'], $this->base->plugin->name . '_nonce' ); ?>" class="button">
                            <?php _e( 'Register Site with Google', 'wp-simple-seo' ); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div class="option">
                <div class="left">
                    <strong><?php _e( 'Ownership Verified', 'wp-simple-seo' ); ?></strong>
                </div>
                <div class="right">
                    <?php
                    if ( $screen['data']['google']['site_verified'] ) {
                        ?>
                        <strong class="success"><?php _e( 'Yes', 'wp-simple-seo' ); ?></strong>
                        <?php
                    } else {
                        ?>
                        <strong class="error"><?php _e( 'No', 'wp-simple-seo' ); ?></strong>
                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wp-simple-seo&action=google_site_verify' ), $this->base->plugin->name . '_' . $screen['name'], $this->base->plugin->name . '_nonce' ); ?>" class="button">
                            <?php _e( 'Verify Site Ownership with Google', 'wp-simple-seo' ); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div class="option">
                <div class="left">
                    <strong><?php _e( 'Sitemap Submitted', 'wp-simple-seo' ); ?></strong>
                </div>
                <div class="right">
                    <?php
                    if ( $screen['data']['google']['sitemap_submitted'] ) {
                        ?>
                        <strong class="success"><?php _e( 'Yes', 'wp-simple-seo' ); ?></strong>
                        <?php
                    } else {
                        ?>
                        <strong class="error"><?php _e( 'No', 'wp-simple-seo' ); ?></strong>
                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wp-simple-seo&action=google_sitemap_submit' ), $this->base->plugin->name . '_' . $screen['name'], $this->base->plugin->name . '_nonce' ); ?>" class="button">
                            <?php _e( 'Submit Sitemap to Google', 'wp-simple-seo' ); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        } else {
            // Not connected to Google
            ?>
            <div class="option">
                <div class="left">
                    <strong><?php _e( 'Google', 'wp-simple-seo' ); ?></strong>
                </div>
                <div class="right">
                    <a href="<?php echo $screen['data']['google']['oauth_url']; ?>" class="button">
                        <?php _e( 'Connect WP Simple SEO to Google', 'wp-simple-seo' ); ?>
                    </a>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!-- Knowledge Graph -->
<div class="panel google-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Knowledge Graph', 'wp-simple-seo' ); ?></h3>

        <div class="option">
            <p class="description">
                <?php _e( 'Google may use this information in its Knowledge Graph, which is a card displayed on the right hand side of Google\'s search results.  View an example.', 'wp-simple-seo' ); ?>
            </p>
        </div>

        <div class="option">
            <div class="left">
                <strong><?php _e( 'Entity Type', 'wp-simple-seo' ); ?></strong>
            </div>
            <div class="right">
                <select name="knowledge_graph[type]" size="1">
                    <?php
                    foreach ( WP_Simple_SEO_Common::get_instance()->get_entity_types() as $entity_type ) {
                        ?>
                        <option value="<?php echo $entity_type['name']; ?>"<?php selected( $this->get_setting( 'general', 'knowledge_graph[type]' ), $entity_type['name'] ); ?>><?php echo $entity_type['label']; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <p class="description">
                    <?php _e( 'The Entity Type determines whether this site is about a Company or a Person.  Google may use this information in its Knowledge Graph.', 'wp-simple-seo' ); ?>
                </p>
            </div>
        </div>
        <div class="option">
            <div class="left">
                <strong><?php _e( 'Name', 'wp-simple-seo' ); ?></strong>
            </div>
            <div class="right">
                <input type="text" name="knowledge_graph[name]" value="<?php echo $this->get_setting( 'general', 'knowledge_graph[name]' ); ?>" class="widefat" />
                <p class="description">
                    <?php _e( 'Your name or company name, depending on the Entity Type chosen above.', 'wp-simple-seo' ); ?>
                </p>
            </div>
        </div>
        <div class="option">
            <div class="left">
                <strong><?php _e( 'Logo', 'wp-simple-seo' ); ?></strong>
            </div>
            <div class="right">
                <?php
                $logo = $this->get_setting( 'general', 'knowledge_graph[logo]' );
                ?> 
                                        
                <span class="wp-media-buttons">
                    <a href="#" class="button insert-media-plugin add_media" data-input="general_knowledge_graph_logo_input" data-output="general_knowledge_graph_logo_output">
                        <span class="wp-media-buttons-icon"></span>
                        <?php _e( 'Select Image', 'wp-simple-seo' ); ?>
                    </a>
                    <a href="#" class="button button-red delete-media-plugin delete_media" data-input="general_knowledge_graph_logo_input" data-output="general_knowledge_graph_logo_output">
                        <?php _e( 'Remove', 'wp-simple-seo' ); ?>
                    </a>
                </span>
            </div>
            <div class="left">
                &nbsp;
            </div>
            <div class="right">
                <input type="hidden" id="general_knowledge_graph_logo_input" name="knowledge_graph[logo]" value="<?php echo $logo; ?>" />
                <?php
                // Output a logo if an image ID exists
                if ( ! empty( $logo ) ) {
                    $logo_src = wp_get_attachment_image_src( $logo, 'thumbnail' );
                    $logo_url = ( ! $logo_src ? '' : $logo_src[0] );
                    ?>
                    <img src="<?php echo $logo_url; ?>" id="general_knowledge_graph_logo_output" />
                    <?php
                } else {
                    ?>
                    <img src="" id="general_knowledge_graph_logo_output" />
                    <?php   
                }
                ?>
                <p class="description">
                    <?php _e( 'Select your Company Logo or personal image.', 'wp-simple-seo' ); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Sitelinks Search Box -->
<div class="panel google-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Sitelinks Search Box', 'wp-simple-seo' ); ?></h3>

        <div class="option">
            <p class="description">
                <?php _e( 'If enabled, Google may display a search box in their search results for certain branded and navigation queries on Google. View an example.', 'wp-simple-seo' ); ?>
            </p>
        </div>

        <div class="option">
            <label for="sitelinks_searchbox_enabled">
                <div class="left">
                    <strong><?php _e( 'Enable?', 'wp-simple-seo' ); ?></strong>
                </div>
                <div class="right">
                    <select name="sitelinks_searchbox[enabled]" id="sitelinks_searchbox_enabled" size="1">
                        <option value="1"<?php selected( $this->get_setting( 'general', 'sitelinks_searchbox[enabled]' ), 1 ); ?>><?php _e( 'Yes', 'wp-simple-seo' ); ?></option>
                        <option value="0"<?php selected( $this->get_setting( 'general', 'sitelinks_searchbox[enabled]' ), 0 ); ?>><?php _e( 'No', 'wp-simple-seo' ); ?></option>
                    </select>
                </div>
            </label>
        </div>
    </div>
</div>

<!-- Bing -->
<div class="panel bing-panel">
    <div class="postbox">
        <h3 class="hndle"><?php _e( 'Verify Site', 'wp-simple-seo' ); ?></h3>

        <div class="option">
            <p class="description">
                <?php echo sprintf( __( 'Submitting your web site, verifying ownership and submitting your XML sitemap to Bing is a manual process.  There are several steps, and we recommend reading our <a href="%s" target="_blank">Documentation</a> on how to do this.', 'wp-simple-seo' ), 'https://www.wpsimpleseo.com/documentation/general-settings#bing' ); ?>
            </p>
        </div>

        <div class="option">
            <div class="left">
                <strong><?php _e( 'Meta Verification', 'wp-simple-seo' ); ?></strong>
            </div>
            <div class="right">
                <input type="text" name="webmaster_tools[bing_verification]" value="<?php echo $this->get_setting( 'general', 'webmaster_tools[bing_verification]' ); ?>" class="widefat" />
                <p class="description">
                    <?php echo sprintf( __( 'Enter the verification string supplied by Bing when you register your site with <a href="%s" target="_blank">Bing\'s Webmaster Tools</a>.', 'wp-simple-seo' ), 'https://www.bing.com/webmaster/configure/verify/ownership?url=' . urlencode( get_bloginfo( 'url' ) ) ); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Save -->
<div class="submit">
    <input type="submit" name="submit" value="<?php _e( 'Save', $this->base->plugin->name ); ?>" class="button button-primary" />
</div>