<?php
/*
Template Name: Default Page Template
*/

get_header(); ?>

<div class="aa-page">

  <!-- Hero -->
  <section class="aa-page-hero">
    <div class="aa-container">
      <h1><?php the_title(); ?></h1>

      <?php if (has_excerpt()) : ?>
        <p class="aa-subtitle"><?php echo get_the_excerpt(); ?></p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Content -->
  <section class="aa-page-content">
    <div class="aa-container aa-container--narrow">
      <?php
      while (have_posts()) :
        the_post();
        the_content();
      endwhile;
      ?>
    </div>
  </section>

</div>

<?php get_footer(); ?>
