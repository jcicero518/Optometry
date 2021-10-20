<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package SUNY_Optometry
 */
class CommunityMeta {

	public $map = [];
	public $meta_values = [];

	public function __construct() {
		$this->map = [
			'testimonial_quote',
			'name',
			'status',
			'hometown',
			'alma_mater',
			'image_headshot',
			'section_title',
			'section_image',
			'section_image_caption'
		];
		$this->setMetaValues();
	}

	private function setMetaValues() {
		global $post;
		foreach ($this->map as $item) {
			$this->meta_values[$item] = get_post_meta($post->ID, $item, true);
		}
	}

	public function getMetaValue($key) {
		return $this->meta_values[$key];
	}

	public function getMetaImage($key, $size = 'full') {
		$attId = $this->getMetaValue($key);
		$src = wp_get_attachment_image_src($attId, $size)[0];
		return $src;
	}
}
$meta_instance = new CommunityMeta();
?>

<aside id="secondary" class="widget-area" role="complementary">
	<div class="widget l-community u-cf">
		<div class="l-community-top">
			<div class="o-community-image">
				<img class="alignright" src="<?= $meta_instance->getMetaImage('image_headshot'); ?>" />
			</div>
			<div class="o-community-copy">
				<h3 class="widget-title"><?= $meta_instance->getMetaValue('name'); ?></h3>
				<p><?= $meta_instance->getMetaValue('status'); ?></p>
				<h5>Hometown</h5>
				<p><?= $meta_instance->getMetaValue('hometown'); ?></p>
				<h5>Alma Mater</h5>
				<p><?= $meta_instance->getMetaValue('alma_mater'); ?></p>
			</div>
		</div>
		<div class="l-community-quote">
			<blockquote>"<?= $meta_instance->getMetaValue('testimonial_quote'); ?>"</blockquote>
		</div>
	</div>

	<hr class="o-divider" />

	<div class="widget l-in-community">
		<h2 class="widget_title"><?= $meta_instance->getMetaValue('section_title'); ?></h2>
		<div class="o-section-image"><img alt="<?= $meta_instance->getMetaValue('section_title'); ?>" src="<?= $meta_instance->getMetaImage('section_image'); ?>" /></div>
		<div class="o-section-caption"><?= $meta_instance->getMetaValue('section_image_caption'); ?></div>
	</div>

</aside><!-- #secondary -->
