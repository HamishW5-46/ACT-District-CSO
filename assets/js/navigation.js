(function () {
  const mobileQuery = window.matchMedia("(max-width: 960px)");

  const directChild = (element, selector) => {
    if (!element) {
      return null;
    }

    return Array.from(element.children).find((child) => child.matches(selector)) || null;
  };

  const closeActiveSubmenu = (container) => {
    const activePanel = container.querySelector(".is-aa-submenu-panel-open");
    const activeItem = container.querySelector(".is-aa-submenu-active");

    if (activePanel) {
      activePanel.classList.remove("is-aa-submenu-panel-open");
      activePanel.setAttribute("aria-hidden", "true");
    }

    if (activeItem) {
      activeItem.classList.remove("is-aa-submenu-active");

      const toggle = directChild(activeItem, ".wp-block-navigation__submenu-icon");
      if (toggle) {
        toggle.setAttribute("aria-expanded", "false");
      }
    }

    container.classList.remove("is-aa-submenu-open");
  };

  const openSubmenu = (container, item, panel, toggle) => {
    closeActiveSubmenu(container);
    item.classList.add("is-aa-submenu-active");
    panel.classList.add("is-aa-submenu-panel-open");
    panel.setAttribute("aria-hidden", "false");
    toggle.setAttribute("aria-expanded", "true");
    container.classList.add("is-aa-submenu-open");
  };

  const addSubmenuHeader = (panel, label, container) => {
    if (directChild(panel, ".aa-mobile-submenu-header")) {
      return;
    }

    const header = document.createElement("li");
    const backButton = document.createElement("button");
    const title = document.createElement("span");

    header.className = "aa-mobile-submenu-header";
    backButton.className = "aa-mobile-submenu-back";
    backButton.type = "button";
    backButton.setAttribute("aria-label", "Back to main menu");
    backButton.textContent = "<";
    title.className = "aa-mobile-submenu-title";
    title.textContent = label;

    backButton.addEventListener(
      "pointerdown",
      (event) => {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
      },
      true
    );

    backButton.addEventListener("click", (event) => {
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();
      const parentToggle = directChild(panel.closest(".wp-block-navigation-submenu"), ".wp-block-navigation__submenu-icon");
      if (parentToggle) {
        parentToggle.focus({ preventScroll: true });
      }

      closeActiveSubmenu(container);
    });

    header.append(backButton, title);
    panel.prepend(header);
  };

  const prepareContainer = (container) => {
    if (!container || container.dataset.aaMobileMenuReady === "true") {
      return;
    }

    container.dataset.aaMobileMenuReady = "true";
    container.classList.add("aa-mobile-menu-enhanced");

    container.querySelectorAll(".wp-block-navigation-submenu").forEach((item) => {
      const panel = directChild(item, ".wp-block-navigation__submenu-container");
      const toggle = directChild(item, ".wp-block-navigation__submenu-icon");
      const labelElement = directChild(item, ".wp-block-navigation-item__content");
      const label = labelElement ? labelElement.textContent.trim() : "Submenu";

      if (!panel || !toggle) {
        return;
      }

      panel.setAttribute("aria-hidden", "true");
      toggle.setAttribute("aria-expanded", "false");
      addSubmenuHeader(panel, label, container);

      toggle.addEventListener(
        "click",
        (event) => {
          if (!mobileQuery.matches || !container.classList.contains("is-menu-open")) {
            return;
          }

          event.preventDefault();
          event.stopPropagation();
          event.stopImmediatePropagation();
          openSubmenu(container, item, panel, toggle);
        },
        true
      );
    });
  };

  const findOpenContainer = (target) => {
    const element = target instanceof Element ? target : null;

    return (
      element?.closest(".wp-block-navigation")?.querySelector(".wp-block-navigation__responsive-container.is-menu-open") ||
      document.querySelector(".wp-block-navigation__responsive-container.is-menu-open")
    );
  };

  document.addEventListener(
    "click",
    (event) => {
      window.requestAnimationFrame(() => {
        const container = findOpenContainer(event.target);

        if (container) {
          prepareContainer(container);
        }

        if (event.target instanceof Element && event.target.closest(".wp-block-navigation__responsive-container-close") && container) {
          closeActiveSubmenu(container);
        }
      });
    },
    true
  );

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") {
      return;
    }

    const container = document.querySelector(".wp-block-navigation__responsive-container.is-menu-open.is-aa-submenu-open");
    if (container) {
      event.preventDefault();
      closeActiveSubmenu(container);
    }
  });

  document.querySelectorAll(".wp-block-navigation__responsive-container.is-menu-open").forEach(prepareContainer);
})();
