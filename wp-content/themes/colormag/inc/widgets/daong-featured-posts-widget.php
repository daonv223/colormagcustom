<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class daong_featured_posts_widget extends ColorMag_Widget {
	public function __construct() {
        $this->widget_cssclass = 'daong-featured-posts';
		$this->widget_description = esc_html__( 'Display latest posts or posts of specific category.', 'colormag' );
		$this->widget_name        = esc_html__( 'Dao Nguyen Featured Posts (Style 1)', 'colormag' );
		$this->settings           = array(
			'title'         => array(
				'type'    => 'text',
				'default' => '',
				'label'   => esc_html__( 'Title:', 'colormag' ),
			),
			'text'          => array(
				'type'    => 'textarea',
				'default' => '',
				'label'   => esc_html__( 'Description', 'colormag' ),
			),
			'number'        => array(
				'type'    => 'number',
				'default' => 4,
				'label'   => esc_html__( 'Number of posts to display:', 'colormag' ),
			),
			'slidesPerView'        => array(
				'type'    => 'select',
				'default' => 3,
				'label'   => esc_html__( 'Number of slides per view to display:', 'colormag' ),
                'choices' => array(
                    3 => '3',
                    4 => '4'
                )
			),
            'align' => [
                    'type' => 'select',
                'default' => 'center',
                'label' => 'Title text align',
                'choices' => array(
                        'center' => 'Center',
                    'left' => 'Left'
                )
            ],
			'type'          => array(
				'type'    => 'radio',
				'default' => 'latest',
				'label'   => '',
				'choices' => array(
					'latest'   => esc_html__( 'Show latest Posts', 'colormag' ),
					'category' => esc_html__( 'Show posts from a category', 'colormag' ),
					'tag'      => esc_html__( 'Show posts from a tag', 'colormag' ),
					'author'   => esc_html__( 'Show posts from an author', 'colormag' ),
				),
			),
			'category'      => array(
				'type'    => 'dropdown_categories',
				'default' => '',
				'label'   => esc_html__( 'Select category', 'colormag' ),
			),
		);

		parent::__construct();

	}

	public function widget( $args, $instance ) {

		global $post;
		$title    = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$text     = isset( $instance['text'] ) ? $instance['text'] : '';
		$number   = empty( $instance['number'] ) ? 4 : $instance['number'];
		$type     = isset( $instance['type'] ) ? $instance['type'] : 'latest';
		$category = isset( $instance['category'] ) ? $instance['category'] : '';
        $slidesPerView = !isset( $instance['slidesPerView'] ) ? 3 : $instance['slidesPerView'];
        $align = $instance['align'] ?? 'center';
        $swiper = 'swiper' . preg_replace( '/.+?-([0-9]+)$/', '$1', $args['widget_id'] );

		// Create the posts query.
		$get_featured_posts = $this->query_posts( $number, $type, $category );

		$this->widget_start( $args );
		?>

		<?php
		// Displays the widget title.
		$this->custom_widget_title($title, $align);

		// Display the description.
		$this->widget_description( $text );
        ?>
        <div class="swiper daong-featured-posts-swiper" id="">
            <div class="swiper-wrapper">
        <?php
		while ( $get_featured_posts->have_posts() ) :
			$get_featured_posts->the_post();
			$featured = 'colormag-featured-post-medium';
			?>
            <div class="cm-post swiper-slide">
		                <?php
		                if ( has_post_thumbnail() ) {
			                $this->the_post_thumbnail( $post->ID, $featured );
		                }
		                ?>

                        <div class="cm-post-content">
			                <?php
			                //colormag_colored_category();

			                // Displays the post title.
			                $this->the_title();

			                // Displays the post meta.
			                $this->entry_meta();
			                ?>
                            <div class="cm-entry-summary">
		                        <?php the_excerpt() . '<span>...</span>'; ?>
                            </div>
                        </div>
                    </div>
			<?php
		endwhile;
        ?>
            </div>
            <div class="navigation swiper-button-prev"></div>
            <div class="navigation swiper-button-next"></div>
        </div>
        <?php
		// Reset Post Data.
		wp_reset_postdata();

		$this->widget_end( $args );
        wp_enqueue_style('daong-featured-posts-widget-stype', COLORMAG_CSS_URL . '/daong-featured-posts.css');
        ?>
        <script>
            const <?= $swiper ?> = new Swiper('#<?= $args['widget_id'] ?> .daong-featured-posts-swiper', {
                slidesPerView: <?= $slidesPerView ?>,
                spaceBetween: 30,

                // Navigation arrows
                navigation: {
                    nextEl: '#<?= $args['widget_id'] ?> .swiper-button-next',
                    prevEl: '#<?= $args['widget_id'] ?> .swiper-button-prev',
                }
            });
        </script>
        <?php
	}

    public function custom_widget_title($title, $align) {
        if (!$title) return;
        if ($align === 'center') $class='cm-widget-title';
        else $class = 'cm-widget-title-2';
	    echo '<' . apply_filters( 'colormag_widget_title_markup', 'h3' ) . " class=\"$class\" " . '><span ' . '>' . esc_html( $title ) . '</span>' . '</' . apply_filters( 'colormag_widget_title_markup', 'h3' ) . '>';
    }
}