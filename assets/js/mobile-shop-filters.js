jQuery(function ($) {
    const $openButton = $('.aac-mobile-filter-toggle');
    const $drawer = $('.aac-mobile-filter-drawer');
    const $overlay = $('.aac-mobile-filter-overlay');
    const $closeButton = $('.aac-mobile-filter-close');
    const $form = $('.aac-mobile-filter-form');

    function openDrawer() {
        $overlay.prop('hidden', false);
        $drawer.attr('aria-hidden', 'false');
        $openButton.attr('aria-expanded', 'true');
        $('body').addClass('aac-filter-drawer-open');
    }

    function closeDrawer() {
        $drawer.attr('aria-hidden', 'true');
        $openButton.attr('aria-expanded', 'false');
        $('body').removeClass('aac-filter-drawer-open');

        setTimeout(function () {
            $overlay.prop('hidden', true);
        }, 250);
    }

    function getFilters(page = 1) {
        const data = $form.serializeArray();

        data.push({ name: 'action', value: 'aac_filter_products' });
        data.push({ name: 'nonce', value: aacShopFilters.nonce });
        data.push({ name: 'paged', value: page });

        const orderby = $('.woocommerce-ordering select[name="orderby"]').val();
        if (orderby) {
            data.push({ name: 'orderby', value: orderby });
        }

        return data;
    }

    function applyFilters(page = 1) {
        $.ajax({
            url: aacShopFilters.ajaxUrl,
            type: 'POST',
            data: getFilters(page),
            beforeSend: function () {
                $('body').addClass('aac-shop-filtering');
                $('.woocommerce ul.products').css('opacity', '0.45');
            },
            success: function (response) {
                if (!response.success) return;

                const $products = $('.woocommerce ul.products');
                const $resultCount = $('.woocommerce-result-count');
                const $pagination = $('.woocommerce-pagination');

                if ($products.length) {
                    $products.replaceWith(response.data.products);
                }

                if ($resultCount.length) {
                    $resultCount.text(response.data.resultCount);
                }

                if ($pagination.length) {
                    $pagination.html(response.data.pagination);
                } else if (response.data.pagination) {
                    $('.woocommerce').append(
                        '<nav class="woocommerce-pagination">' + response.data.pagination + '</nav>'
                    );
                }

                const params = new URLSearchParams($form.serialize());
                params.set('paged', page);
                history.pushState({}, '', '?' + params.toString());

                closeDrawer();

                $('html, body').animate({
                    scrollTop: $('.woocommerce').offset().top - 120
                }, 250);
            },
            complete: function () {
                $('body').removeClass('aac-shop-filtering');
                $('.woocommerce ul.products').css('opacity', '1');
            }
        });
    }

    $openButton.on('click', openDrawer);
    $closeButton.on('click', closeDrawer);
    $overlay.on('click', closeDrawer);

    $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
            closeDrawer();
        }
    });

    $form.on('submit', function (event) {
        event.preventDefault();
        applyFilters(1);
    });

    $('.aac-filter-clear').on('click', function () {
        $form[0].reset();
    });

    
    $(document).on('click', '.woocommerce-pagination a', function (event) {
        event.preventDefault();

        const href = $(this).attr('href');
        let page = 1;

        const match = href.match(/paged=([0-9]+)/) || href.match(/page\/([0-9]+)/);

        if (match && match[1]) {
            page = parseInt(match[1], 10);
        }

        applyFilters(page);
    });
});