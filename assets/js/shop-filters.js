jQuery(function ($) {
    const mobileMedia = window.matchMedia('(max-width: 768px)');
    const $openButton = $('.aac-mobile-filter-toggle');
    const $drawer = $('.aac-mobile-filter-drawer');
    const $overlay = $('.aac-mobile-filter-overlay');
    const $closeButton = $('.aac-mobile-filter-close');
    const $forms = $('.aac-shop-filter-form');
    const $desktopForm = $('.aac-shop-filter-form--desktop');
    const $mobileForm = $('.aac-shop-filter-form--mobile');

    function getActiveForm() {
        if ($('body').hasClass('aac-filter-drawer-open') && $mobileForm.length) {
            return $mobileForm;
        }

        if (mobileMedia.matches && $mobileForm.length) {
            return $mobileForm;
        }

        if ($desktopForm.length) {
            return $desktopForm;
        }

        return $forms.first();
    }

    function syncForms($source, $target) {
        if (!$source.length || !$target.length || $source[0] === $target[0]) {
            return;
        }

        $target.find('input').each(function () {
            const $targetInput = $(this);
            const name = $targetInput.attr('name');

            if (!name) {
                return;
            }

            if ($targetInput.is(':checkbox, :radio')) {
                const value = $targetInput.val();
                const checked = $source
                    .find('input[name="' + name + '"][value="' + value + '"]')
                    .prop('checked');

                $targetInput.prop('checked', Boolean(checked));
                return;
            }

            const $sourceInput = $source.find('input[name="' + name + '"]');

            if ($sourceInput.length) {
                $targetInput.val($sourceInput.val());
            }
        });
    }

    function openDrawer() {
        syncForms($desktopForm, $mobileForm);
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

    function closeDrawerOnDesktop(event) {
        if (event.matches) {
            return;
        }

        closeDrawer();
    }

    function getFilters(page = 1, $form = getActiveForm()) {
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

    function applyFilters(page = 1, $form = getActiveForm()) {
        $.ajax({
            url: aacShopFilters.ajaxUrl,
            type: 'POST',
            data: getFilters(page, $form),

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

                syncForms($form, $forms.not($form).first());

                if ($('body').hasClass('aac-filter-drawer-open')) {
                    closeDrawer();
                }

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

    if (typeof mobileMedia.addEventListener === 'function') {
        mobileMedia.addEventListener('change', closeDrawerOnDesktop);
    } else if (typeof mobileMedia.addListener === 'function') {
        mobileMedia.addListener(closeDrawerOnDesktop);
    }

    $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
            closeDrawer();
        }
    });

    $forms.on('submit', function (event) {
        event.preventDefault();
        applyFilters(1, $(this));
    });

    $(document).on('click', '.aac-filter-clear', function () {
        const $form = $(this).closest('.aac-shop-filter-form');

        if ($form.length) {
            $form[0].reset();
            applyFilters(1, $form);
        }
    });

    $(document).on('submit', '.woocommerce-ordering', function (event) {
        event.preventDefault();
    });

    $(document).on(
        'change',
        '.woocommerce-ordering select[name="orderby"]',
        function (event) {
            event.preventDefault();
            applyFilters(1, getActiveForm());
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

        applyFilters(page, getActiveForm());
    });
});
