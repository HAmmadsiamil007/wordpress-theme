<?php
/**
 * Template Name: Gallery Page
 * Description: Gallery/lookbook page with filterable grid
 *
 * @package Opulentia
 */

get_header();
?>

<main id="primary" class="site-main">
    <section class="page-hero">
        <div class="container">
            <h1 class="page-hero__title"><?php the_title(); ?></h1>
            <p class="page-hero__subtitle"><?php esc_html_e( 'Our Collections', 'opulentia' ); ?></p>
        </div>
    </section>

    <section class="gallery-section">
        <div class="container">
            <div class="gallery-filters">
                <button class="gallery-filters__btn active" data-filter="all"><?php esc_html_e( 'All', 'opulentia' ); ?></button>
                <button class="gallery-filters__btn" data-filter="classics"><?php esc_html_e( 'Classics', 'opulentia' ); ?></button>
                <button class="gallery-filters__btn" data-filter="modern"><?php esc_html_e( 'Modern', 'opulentia' ); ?></button>
                <button class="gallery-filters__btn" data-filter="limited"><?php esc_html_e( 'Limited Edition', 'opulentia' ); ?></button>
            </div>

            <div class="gallery-grid">
                <?php
                $gallery_items = array(
                    array(
                        'title' => 'Classic Loafer',
                        'category' => 'classics',
                        'image' => '',
                    ),
                    array(
                        'title' => 'Urban Runner',
                        'category' => 'modern',
                        'image' => '',
                    ),
                    array(
                        'title' => 'Heritage Oxford',
                        'category' => 'classics',
                        'image' => '',
                    ),
                    array(
                        'title' => 'Midnight Edition',
                        'category' => 'limited',
                        'image' => '',
                    ),
                    array(
                        'title' => 'Sport Elite',
                        'category' => 'modern',
                        'image' => '',
                    ),
                    array(
                        'title' => 'Suede Master',
                        'category' => 'classics',
                        'image' => '',
                    ),
                );

                foreach ( $gallery_items as $item ) :
                ?>
                    <div class="gallery-item" data-category="<?php echo esc_attr( $item['category'] ); ?>">
                        <?php if ( $item['image'] ) : ?>
                            <img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" class="gallery-item__image">
                        <?php else : ?>
                            <svg class="gallery-item__image" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="400" height="300" fill="#1a1a1a"/>
                                <path d="M50 250 Q200 100 350 250" stroke="#8B4513" stroke-width="3" fill="none"/>
                                <ellipse cx="200" cy="260" rx="150" ry="20" fill="#8B4513" opacity="0.3"/>
                                <path d="M80 230 Q200 120 320 230" fill="#A0522D"/>
                                <path d="M120 200 Q200 140 280 200" fill="#8B4513"/>
                            </svg>
                        <?php endif; ?>
                        <div class="gallery-item__overlay">
                            <span class="gallery-item__category"><?php echo esc_html( ucfirst( $item['category'] ) ); ?></span>
                            <h3 class="gallery-item__title"><?php echo esc_html( $item['title'] ); ?></h3>
                            <a href="#" class="btn btn--small btn--outline"><?php esc_html_e( 'View Details', 'opulentia' ); ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php get_template_part( 'template-parts/features' ); ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.gallery-filters__btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            filterButtons.forEach(function(btn) {
                btn.classList.remove('active');
            });
            this.classList.add('active');

            galleryItems.forEach(function(item) {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php
get_footer();
