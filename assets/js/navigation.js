(function () {
  const mobileQuery = window.matchMedia("(max-width: 960px)");

  const directChild = (element, selector) => {
    if (!element) {
      return null;
    }

    return Array.from(element.children).find((child) => child.matches(selector)) || null;
  };

  const activeSubmenuItems = (container) => Array.from(container.querySelectorAll(".is-aa-submenu-active"));

  const submenuDepth = (item) => {
    let depth = 0;
    let parent = item.parentElement?.closest(".wp-block-navigation-submenu");

    while (parent) {
      depth += 1;
      parent = parent.parentElement?.closest(".wp-block-navigation-submenu");
    }

    return depth;
  };

  const closeSubmenu = (container, item) => {
    if (!item) {
      return;
    }

    const activePanel = directChild(item, ".wp-block-navigation__submenu-container");
    const toggle = directChild(item, ".wp-block-navigation__submenu-icon");

    if (activePanel) {
      activePanel.classList.remove("is-aa-submenu-panel-open");
      activePanel.setAttribute("aria-hidden", "true");
      activePanel.style.removeProperty("z-index");
    }

    if (toggle) {
      toggle.setAttribute("aria-expanded", "false");
    }

    item.classList.remove("is-aa-submenu-active");

    if (!container.querySelector(".is-aa-submenu-panel-open")) {
      container.classList.remove("is-aa-submenu-open");
      container.querySelectorAll(".is-aa-submenu-background-item").forEach((backgroundItem) => {
        backgroundItem.classList.remove("is-aa-submenu-background-item");
      });
    }
  };

  const closeAllSubmenus = (container) => {
    activeSubmenuItems(container).reverse().forEach((item) => closeSubmenu(container, item));
    container.classList.remove("is-aa-submenu-open");
    container.querySelectorAll(".is-aa-submenu-background-item").forEach((backgroundItem) => {
      backgroundItem.classList.remove("is-aa-submenu-background-item");
    });
  };

  const closeDescendantSubmenus = (container, item) => {
    Array.from(item.querySelectorAll(".is-aa-submenu-active"))
      .reverse()
      .forEach((activeItem) => closeSubmenu(container, activeItem));
  };

  const closeSiblingSubmenus = (container, item) => {
    activeSubmenuItems(container)
      .reverse()
      .forEach((activeItem) => {
        if (activeItem === item || activeItem.contains(item) || item.contains(activeItem)) {
          return;
        }

        closeSubmenu(container, activeItem);
      });
  };

  const closeTopSubmenu = (container) => {
    const activeItems = activeSubmenuItems(container);
    closeSubmenu(container, activeItems[activeItems.length - 1]);
  };

  const openSubmenu = (container, item, panel, toggle) => {
    closeDescendantSubmenus(container, item);
    closeSiblingSubmenus(container, item);
    Array.from(item.parentElement?.children || []).forEach((sibling) => {
      sibling.classList.toggle("is-aa-submenu-background-item", sibling !== item);
    });
    item.classList.add("is-aa-submenu-active");
    panel.classList.add("is-aa-submenu-panel-open");
    panel.setAttribute("aria-hidden", "false");
    panel.style.zIndex = String(4 + submenuDepth(item));
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
      const parentItem = panel.closest(".wp-block-navigation-submenu");
      const parentToggle = directChild(parentItem, ".wp-block-navigation__submenu-icon");
      if (parentToggle) {
        parentToggle.focus({ preventScroll: true });
      }

      closeSubmenu(container, parentItem);
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

      ["pointerdown", "touchstart"].forEach((eventName) => {
        toggle.addEventListener(
          eventName,
          (event) => {
            if (!mobileQuery.matches || !container.classList.contains("is-menu-open")) {
              return;
            }

            event.stopPropagation();
            event.stopImmediatePropagation();
          },
          true
        );
      });

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

  const prepareOpenContainers = () => {
    document.querySelectorAll(".wp-block-navigation__responsive-container.is-menu-open").forEach((container) => {
      prepareContainer(container);

      if (container.querySelector(".is-aa-submenu-panel-open")) {
        container.classList.add("is-aa-submenu-open");
      }
    });
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
          closeAllSubmenus(container);
        }
      });
    },
    true
  );

  if ("MutationObserver" in window) {
    const observer = new MutationObserver(prepareOpenContainers);
    observer.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ["class"],
      subtree: true,
    });
  }

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") {
      return;
    }

    const container = document.querySelector(".wp-block-navigation__responsive-container.is-menu-open.is-aa-submenu-open");
    if (container) {
      event.preventDefault();
      closeTopSubmenu(container);
    }
  });

  prepareOpenContainers();
})();
