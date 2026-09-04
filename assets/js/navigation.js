(function () {
  const mobileQuery = window.matchMedia("(max-width: 960px)");
  const containerSelector = ".wp-block-navigation__responsive-container";
  const openContainerSelector = `${containerSelector}.is-menu-open`;
  const submenuSelector = ".wp-block-navigation-submenu";
  const panelSelector = ".wp-block-navigation__submenu-container";
  const toggleSelector = ".wp-block-navigation__submenu-icon";

  const directChild = (element, selector) => {
    if (!element) {
      return null;
    }

    return Array.from(element.children).find((child) => child.matches(selector)) || null;
  };

  const topLevelItems = (item) => Array.from(item.parentElement?.children || []);
  const activeSubmenuItems = (container) => Array.from(container.querySelectorAll(".is-aa-submenu-active"));

  const submenuDepth = (item) => {
    let depth = 0;
    let parent = item.parentElement?.closest(submenuSelector);

    while (parent) {
      depth += 1;
      parent = parent.parentElement?.closest(submenuSelector);
    }

    return depth;
  };

  const clearBackgroundItems = (container) => {
    container.querySelectorAll(".is-aa-submenu-background-item").forEach((item) => {
      item.classList.remove("is-aa-submenu-background-item");
    });
  };

  const markBackgroundItems = (item) => {
    topLevelItems(item).forEach((sibling) => {
      sibling.classList.toggle("is-aa-submenu-background-item", sibling !== item);
    });
  };

  const keepMenuOpen = (container) => {
    container.classList.add("has-modal-open", "is-menu-open", "is-aa-submenu-open");
  };

  const closeSubmenu = (container, item) => {
    if (!container || !item) {
      return;
    }

    const panel = directChild(item, panelSelector);
    const toggle = directChild(item, toggleSelector);

    if (panel) {
      panel.classList.remove("is-aa-submenu-panel-open");
      panel.setAttribute("aria-hidden", "true");
      panel.style.removeProperty("z-index");
    }

    if (toggle) {
      toggle.setAttribute("aria-expanded", "false");
    }

    item.classList.remove("is-aa-submenu-active");

    if (!container.querySelector(".is-aa-submenu-panel-open")) {
      container.classList.remove("is-aa-submenu-open");
      clearBackgroundItems(container);
    }
  };

  const closeAllSubmenus = (container) => {
    if (!container) {
      return;
    }

    activeSubmenuItems(container).reverse().forEach((item) => closeSubmenu(container, item));
    container.classList.remove("is-aa-submenu-open");
    clearBackgroundItems(container);
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

  const openSubmenu = (container, item, panel, toggle) => {
    closeDescendantSubmenus(container, item);
    closeSiblingSubmenus(container, item);
    markBackgroundItems(item);
    keepMenuOpen(container);

    item.classList.add("is-aa-submenu-active");
    panel.classList.add("is-aa-submenu-panel-open");
    panel.setAttribute("aria-hidden", "false");
    panel.style.zIndex = String(8 + submenuDepth(item));
    toggle.setAttribute("aria-expanded", "true");

    window.requestAnimationFrame(() => keepMenuOpen(container));
    window.setTimeout(() => keepMenuOpen(container), 0);
    window.setTimeout(() => keepMenuOpen(container), 120);
  };

  const addSubmenuHeader = (panel, label) => {
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

    header.append(backButton, title);
    panel.prepend(header);
  };

  const prepareContainer = (container) => {
    if (!container || container.dataset.aaMobileMenuReady === "true") {
      return;
    }

    container.dataset.aaMobileMenuReady = "true";
    container.classList.add("aa-mobile-menu-enhanced");

    container.querySelectorAll(submenuSelector).forEach((item) => {
      const panel = directChild(item, panelSelector);
      const toggle = directChild(item, toggleSelector);
      const labelElement = directChild(item, ".wp-block-navigation-item__content");
      const label = labelElement ? labelElement.textContent.trim() : "Submenu";

      if (!panel || !toggle) {
        return;
      }

      panel.setAttribute("aria-hidden", "true");
      toggle.setAttribute("aria-expanded", "false");
      addSubmenuHeader(panel, label);
    });
  };

  const prepareContainers = () => {
    document.querySelectorAll(containerSelector).forEach(prepareContainer);
  };

  const closestOpenContainer = (element) => element?.closest(openContainerSelector);

  const handleNavigationClick = (event) => {
    const target = event.target instanceof Element ? event.target : null;

    if (!target) {
      return;
    }

    const openButton = target.closest(".wp-block-navigation__responsive-container-open");
    if (openButton) {
      window.requestAnimationFrame(prepareContainers);
      return;
    }

    const closeButton = target.closest(".wp-block-navigation__responsive-container-close");
    if (closeButton) {
      closeAllSubmenus(document.querySelector(openContainerSelector));
      return;
    }

    const backButton = target.closest(".aa-mobile-submenu-back");
    if (backButton) {
      const container = closestOpenContainer(backButton);
      const item = backButton.closest(submenuSelector);

      if (!mobileQuery.matches || !container || !item) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();
      closeSubmenu(container, item);
      return;
    }

    const toggle = target.closest(toggleSelector);
    if (!toggle) {
      return;
    }

    const container = closestOpenContainer(toggle);
    const item = toggle.closest(submenuSelector);
    const panel = directChild(item, panelSelector);

    if (!mobileQuery.matches || !container || !item || !panel) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();
    openSubmenu(container, item, panel, toggle);
  };

  window.addEventListener("click", handleNavigationClick, true);

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") {
      return;
    }

    const container = document.querySelector(`${openContainerSelector}.is-aa-submenu-open`);
    const activeItems = container ? activeSubmenuItems(container) : [];

    if (container && activeItems.length) {
      event.preventDefault();
      closeSubmenu(container, activeItems[activeItems.length - 1]);
    }
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", prepareContainers, { once: true });
  } else {
    prepareContainers();
  }
})();
