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

        data.push({
            name: 'action',
            value: 'aac_filter_products'
        });

        data.push({
            name: 'nonce',
            value: aacShopFilters.nonce
        });

        data.push({
            name: 'paged',
            value: page
        });

        const orderby = $('.woocommerce-ordering select[name="orderby"]').val();

        if (orderby) {
            data.push({
                name: 'orderby',
                value: orderby
            });
        }

        return data;
    }

    function replaceProducts(productsHtml) {
        const $currentProducts = $('.woocommerce ul.products');
        const $noProductsMessage = $('.woocommerce > .woocommerce-info');

        if ($currentProducts.length) {
            $currentProducts.replaceWith(productsHtml);
            return;
        }

        if ($noProductsMessage.length) {
            $noProductsMessage.replaceWith(productsHtml);
            return;
        }

        const $resultCount = $('.woocommerce-result-count');

        if ($resultCount.length) {
            $resultCount.after(productsHtml);
        }
    }

    function updatePagination(paginationHtml) {
        const $pagination = $('.woocommerce-pagination');

        if (paginationHtml) {
            if ($pagination.length) {
                $pagination.replaceWith(
                    '<nav class="woocommerce-pagination">' +
                    paginationHtml +
                    '</nav>'
                );
            } else {
                $('.woocommerce').append(
                    '<nav class="woocommerce-pagination">' +
                    paginationHtml +
                    '</nav>'
                );
            }

            return;
        }

        $pagination.remove();
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
                if (!response.success || !response.data) {
                    return;
                }

                replaceProducts(response.data.products);

                const $resultCount = $('.woocommerce-result-count');

                if ($resultCount.length) {
                    $resultCount.text(response.data.resultCount);
                }

                updatePagination(response.data.pagination);

                closeDrawer();

                const $woocommerce = $('.woocommerce');

                if ($woocommerce.length) {
                    $('html, body').animate(
                        {
                            scrollTop: $woocommerce.offset().top - 120
                        },
                        250
                    );
                }
            },

            error: function () {
                console.error('Unable to filter products.');
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

    $(document).on(
        'change',
        '.woocommerce-ordering select[name="orderby"]',
        function () {
            applyFilters(1);
        }
    );

    $(document).on('click', '.woocommerce-pagination a', function (event) {
        event.preventDefault();

        const href = $(this).attr('href') || '';
        const match =
            href.match(/[?&]paged=([0-9]+)/) ||
            href.match(/\/page\/([0-9]+)/);

        const page = match && match[1]
            ? parseInt(match[1], 10)
            : 1;

        applyFilters(page);
    });
});