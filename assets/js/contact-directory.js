document.addEventListener("DOMContentLoaded", () => {
    const directory = document.querySelector(".aa-contact-directory");

    if (!directory) {
        return;
    }

    const modal = directory.querySelector("[data-aa-contact-modal]");
    const form = directory.querySelector("[data-aa-contact-form]");
    const title = directory.querySelector("[data-aa-contact-title]");
    const recipientInput = directory.querySelector(
        "[data-aa-contact-recipient]"
    );
    const openedAtInput = directory.querySelector(
        "[data-aa-contact-opened-at]"
    );
    const status = directory.querySelector("[data-aa-contact-status]");
    const submitButton = directory.querySelector(
        "[data-aa-contact-submit]"
    );
    const turnstileContainer = directory.querySelector(
        "[data-aa-contact-turnstile]"
    );

    let previouslyFocusedElement = null;
    let turnstileWidgetId = null;
    let turnstileRenderTimer = null;


    function clearStatus() {
        status.textContent = "";
        status.classList.remove(
            "is-success",
            "is-error"
        );
    }


    function showStatus(message, statusClass) {
        status.textContent = message;
        status.classList.remove(
            "is-success",
            "is-error"
        );
        status.classList.add(statusClass);
    }


    function isTurnstileEnabled() {
        return Boolean(
            window.aaContactDirectory?.turnstile?.enabled &&
            window.aaContactDirectory.turnstile.siteKey &&
            turnstileContainer
        );
    }


    function resetTurnstile() {
        if (
            isTurnstileEnabled() &&
            window.turnstile &&
            turnstileWidgetId !== null
        ) {
            window.turnstile.reset(turnstileWidgetId);
        }
    }


    function renderTurnstile() {
        if (!isTurnstileEnabled()) {
            return;
        }

        if (
            !window.turnstile ||
            typeof window.turnstile.render !== "function"
        ) {
            window.clearTimeout(turnstileRenderTimer);
            turnstileRenderTimer = window.setTimeout(
                renderTurnstile,
                100
            );
            return;
        }

        if (turnstileWidgetId !== null) {
            resetTurnstile();
            return;
        }

        turnstileWidgetId = window.turnstile.render(
            turnstileContainer,
            {
                sitekey: window.aaContactDirectory.turnstile.siteKey,
                action: window.aaContactDirectory.turnstile.action,
                theme: "auto",
                "error-callback": () => {
                    showStatus(
                        "Verification could not be completed. Please try again.",
                        "is-error"
                    );
                },
                "expired-callback": () => {
                    resetTurnstile();
                },
            }
        );
    }


    function getTurnstileToken() {
        if (!isTurnstileEnabled()) {
            return "";
        }

        if (
            !window.turnstile ||
            turnstileWidgetId === null
        ) {
            return "";
        }

        return window.turnstile.getResponse(turnstileWidgetId) || "";
    }


    function openModal(button) {
        const recipient = button.dataset.recipient;
        const recipientName = button.dataset.recipientName;

        previouslyFocusedElement = document.activeElement;

        recipientInput.value = recipient;
        openedAtInput.value = Math.floor(Date.now() / 1000);

        title.textContent = `Contact ${recipientName}`;

        clearStatus();

        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");

        document.body.classList.add("aa-contact-modal-open");

        renderTurnstile();

        const firstInput = form.querySelector(
            'input:not([type="hidden"])'
        );

        if (firstInput) {
            firstInput.focus();
        }
    }


    function closeModal() {
        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");

        document.body.classList.remove("aa-contact-modal-open");

        clearStatus();
        form.reset();
        resetTurnstile();

        if (previouslyFocusedElement) {
            previouslyFocusedElement.focus();
        }
    }


    directory
        .querySelectorAll("[data-aa-contact-open]")
        .forEach((button) => {
            button.addEventListener("click", () => {
                openModal(button);
            });
        });


    directory
        .querySelectorAll("[data-aa-contact-close]")
        .forEach((button) => {
            button.addEventListener("click", closeModal);
        });


    modal.addEventListener("click", (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });


    document.addEventListener("keydown", (event) => {
        if (
            event.key === "Escape" &&
            modal.classList.contains("is-open")
        ) {
            closeModal();
        }
    });


    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        if (!form.reportValidity()) {
            return;
        }

        clearStatus();

        const turnstileToken = getTurnstileToken();

        if (
            isTurnstileEnabled() &&
            !turnstileToken
        ) {
            showStatus(
                "Please complete the verification before sending.",
                "is-error"
            );
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = "Sending...";

        const formData = new FormData(form);

        if (isTurnstileEnabled()) {
            formData.set(
                "cf-turnstile-response",
                turnstileToken
            );
        }

        formData.append(
            "action",
            "aa_contact_directory_submit"
        );

        formData.append(
            "nonce",
            aaContactDirectory.nonce
        );

        try {
            const response = await fetch(
                aaContactDirectory.ajaxUrl,
                {
                    method: "POST",
                    credentials: "same-origin",
                    body: formData,
                }
            );

            const result = await response.json();

            if (!result.success) {
                throw new Error(
                    result.data?.message ||
                    "Your message could not be sent."
                );
            }

            showStatus(
                result.data.message,
                "is-success"
            );

            /*
             * Preserve recipient/opened_at before reset if desired,
             * but after success we don't need them anymore.
             */
            form.reset();

            submitButton.textContent = "Sent";

        } catch (error) {
            showStatus(
                error.message,
                "is-error"
            );

        } finally {
            resetTurnstile();
            submitButton.disabled = false;

            if (
                !status.classList.contains("is-success")
            ) {
                submitButton.textContent = "Send message";
            }
        }
    });
});
