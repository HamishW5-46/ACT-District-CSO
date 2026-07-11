<?php
get_header();
?>

<main class="container">
  <section class="section-preview">
    <h1>Notices & Updates</h1>

    <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
        <article class="preview-item">
          <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <p class="post-date"><?php echo get_the_date('j M Y'); ?></p>
          <?php the_excerpt(); ?>
        </article>
      <?php endwhile; ?>

      <?php the_posts_navigation(); ?>

    <?php else : ?>
      <p>No notices or updates at the moment.</p>
    <?php endif; ?>
  </section>
</main>

<?php get_footer(); ?>
