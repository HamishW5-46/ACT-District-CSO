(function initShopFilters(attempts) {
  if (!window.jQuery) {
    window.setTimeout(
      function () {
        initShopFilters(attempts + 1);
      },
      attempts < 100 ? 50 : 250,
    );
    return;
  }

  window.jQuery(function ($) {
    const mobileMedia = window.matchMedia("(max-width: 768px)");
    const $openButton = $(".aac-mobile-filter-toggle");
    const $drawer = $(".aac-mobile-filter-drawer");
    const $overlay = $(".aac-mobile-filter-overlay");
    const $closeButton = $(".aac-mobile-filter-close");
    const $forms = $(".aac-shop-filter-form");
    const $desktopForm = $(".aac-shop-filter-form--desktop");
    const $mobileForm = $(".aac-shop-filter-form--mobile");

    function getActiveForm() {
      if ($("body").hasClass("aac-filter-drawer-open") && $mobileForm.length) {
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

      $target.find("input").each(function () {
        const $targetInput = $(this);
        const name = $targetInput.attr("name");

        if (!name) {
          return;
        }

        if ($targetInput.is(":checkbox, :radio")) {
          const value = $targetInput.val();
          const checked = $source
            .find('input[name="' + name + '"][value="' + value + '"]')
            .prop("checked");

          $targetInput.prop("checked", Boolean(checked));
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
      $overlay.prop("hidden", false);
      $drawer.attr("aria-hidden", "false");
      $openButton.attr("aria-expanded", "true");
      $("body").addClass("aac-filter-drawer-open");
    }

    function closeDrawer() {
      $drawer.attr("aria-hidden", "true");
      $openButton.attr("aria-expanded", "false");
      $("body").removeClass("aac-filter-drawer-open");

      setTimeout(function () {
        $overlay.prop("hidden", true);
      }, 250);
    }

    function closeDrawerOnDesktop(event) {
      if (event.matches) {
        return;
      }

      closeDrawer();
    }

    function appendFormValue(params, name, value) {
      if (!name || value === null || value === undefined || value === "") {
        return;
      }

      params.append(name, value);
    }

    function filterUrl($form) {
      const action = $form.attr("action") || window.location.pathname;
      const url = new URL(action, window.location.origin);
      const params = new URLSearchParams();

      $form.serializeArray().forEach(function (field) {
        appendFormValue(params, field.name, field.value);
      });

      const orderby = $('.woocommerce-ordering select[name="orderby"]').val();

      if (orderby) {
        params.set("orderby", orderby);
      }

      url.search = params.toString();
      return url.toString();
    }

    function navigateWithFilters($form) {
      if (!$form.length) {
        return;
      }

      window.location.href = filterUrl($form);
    }

    $openButton.on("click", openDrawer);
    $closeButton.on("click", closeDrawer);
    $overlay.on("click", closeDrawer);

    if (typeof mobileMedia.addEventListener === "function") {
      mobileMedia.addEventListener("change", closeDrawerOnDesktop);
    } else if (typeof mobileMedia.addListener === "function") {
      mobileMedia.addListener(closeDrawerOnDesktop);
    }

    $(document).on("keydown", function (event) {
      if (event.key === "Escape") {
        closeDrawer();
      }
    });

    $forms.on("submit", function (event) {
      event.preventDefault();
      navigateWithFilters($(this));
    });

    $(document).on("click", ".aac-filter-clear", function () {
      const $form = $(this).closest(".aac-shop-filter-form");

      if (!$form.length) {
        return;
      }

      $form[0].reset();
    });

    $(document).on("submit", ".woocommerce-ordering", function (event) {
      event.preventDefault();
      navigateWithFilters(getActiveForm());
    });

    $(document).on(
      "change",
      '.woocommerce-ordering select[name="orderby"]',
      function (event) {
        event.preventDefault();
        navigateWithFilters(getActiveForm());
      },
    );
  });
})(0);
