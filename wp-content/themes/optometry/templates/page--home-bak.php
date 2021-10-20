<?php
/**
 * The template for displaying the home page.
 *
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package SUNY_Optometry
 */

get_header(); ?>
	<section class="site-top-hero section-row">
		<div class="caption">
			<div class="wrap">
				<div class="caption-title">
					<h2 class="title">A Growing Field,<br /> a Caring Profession</h2>
				</div>
				<div class="caption-desc">
					<p>
						A Doctor of Optometry is an independent primary healthcare provider who examines
						the visual system, the eye and associated structures, as well as diagnoses,
						treats and manages related diseases and disorders. <a href="#">READ MORE</a>
					</p>
				</div>
			</div>
		</div>
	</section>
	<section class="site-optometrist section-row">
		<div class="section-inner-wrapper">
			<div class="wrap">
				<aside class="caption">
					<div class="caption-title">
						<h2 class="title">What does an Optometrist do?</h2>
					</div>
					<div class="caption-desc">
						<p>
							There are multiple career options for students graduating with a Doctor of
							Optometry (OD) degree: private practices, multidisciplinary medical practices,
							hospitals, teaching institutions, research positions, community health centers
							and the ophthalmic industry. Optometrists can also build successful careers in
							the military, public health or government service.
						</p>
						<p>
							Many optometrists also focus on specific sub-specialties, such as pediatrics,
							vision therapy, ocular disease, head trauma and other areas. <a href="#">READ MORE</a>
						</p>
					</div>
				</aside>
			</div>
			<section class="site-optometrist section-row-mobile">
				<div class="caption">
					<div class="caption-title">
						<h2 class="title">What does an Optometrist do?</h2>
					</div>
					<div class="caption-desc">
						<p>
							There are multiple career options for students graduating with a Doctor of
							Optometry (OD) degree: private practices, multidisciplinary medical practices,
							hospitals, teaching institutions, research positions, community health centers
							and the ophthalmic industry. Optometrists can also build successful careers in
							the military, public health or government service.
						</p>
						<p>
							Many optometrists also focus on specific sub-specialties, such as pediatrics,
							vision therapy, ocular disease, head trauma and other areas. <a href="#">READ MORE</a>
						</p>
					</div>
				</div>
			</section>
		</div>

	</section>

	<section class="site-president section-row">
		<div class="wrap">
			<div class="caption-title">
				<h2 class="title">Letter From the President</h2>
			</div>
			<aside class="section-image">
				<img alt="Letter from the President" src="<?= get_stylesheet_directory_uri() . '/assets/images/president.jpg'; ?>" />
			</aside>
			<div class="caption">

				<div class="caption-desc">
					<p>Dear Friends,</p>
					<p>
						Optometry is an ever-changing and dynamic healthcare profession, and our goal at
						the State University of New York College of Optometry is to be innovative, bold and
						impactful in the ways in which we accomplish our mission. SUNY Optometry is dedicated
						to the education of optometrists, the advancement of eye and vision care through research
						and graduate education and the care of our community through comprehensive visual health
						services. The College is the only institution of its kind in New York State and the
						surrounding region. In fact, we’ve educated approximately 60% of all practicing
						optometrists in New York State. <a href="#">READ MORE</a>
					</p>
				</div>
			</div>
		</div>
	</section>

<?php
get_footer();
