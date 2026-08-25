document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll("[data-aa-form]");

    if (!forms.length) {
        return;
    }

    const turnstileWidgets = new WeakMap();

    function formId(form) {
        return form.dataset.formId || form.querySelector('input[name="form_id"]')?.value || "";
    }

    function isTurnstileEnabled(form) {
        return Boolean(
            window.aaForms?.turnstile?.enabled &&
            window.aaForms.turnstile.siteKey &&
            form.querySelector("[data-aa-form-turnstile]")
        );
    }

    function showStatus(form, message, statusClass) {
        const status = form.querySelector("[data-aa-form-status]");

        if (!status) {
            return;
        }

        status.textContent = message;
        status.classList.remove("is-success", "is-error");
        status.classList.add(statusClass);
    }

    function clearStatus(form) {
        const status = form.querySelector("[data-aa-form-status]");

        if (!status) {
            return;
        }

        status.textContent = "";
        status.classList.remove("is-success", "is-error");
    }

    function resetTurnstile(form) {
        const widgetId = turnstileWidgets.get(form);

        if (
            isTurnstileEnabled(form) &&
            window.turnstile &&
            widgetId !== undefined
        ) {
            window.turnstile.reset(widgetId);
        }
    }

    function renderTurnstile(form) {
        if (!isTurnstileEnabled(form)) {
            return;
        }

        if (!window.turnstile || typeof window.turnstile.render !== "function") {
            window.setTimeout(() => renderTurnstile(form), 100);
            return;
        }

        if (turnstileWidgets.has(form)) {
            resetTurnstile(form);
            return;
        }

        const id = formId(form);
        const container = form.querySelector("[data-aa-form-turnstile]");
        const action = window.aaForms.turnstile.actions?.[id] || `aa_form_${id}`;

        const widgetId = window.turnstile.render(container, {
            sitekey: window.aaForms.turnstile.siteKey,
            action,
            theme: "auto",
            "error-callback": () => {
                showStatus(
                    form,
                    "Verification could not be completed. Please try again.",
                    "is-error"
                );
            },
            "expired-callback": () => {
                resetTurnstile(form);
            },
        });

        turnstileWidgets.set(form, widgetId);
    }

    function getTurnstileToken(form) {
        const widgetId = turnstileWidgets.get(form);

        if (!isTurnstileEnabled(form) || !window.turnstile || widgetId === undefined) {
            return "";
        }

        return window.turnstile.getResponse(widgetId) || "";
    }

    function prepareOpenedAt(form) {
        const openedAt = form.querySelector("[data-aa-form-opened-at]");

        if (openedAt && !openedAt.value) {
            openedAt.value = Math.floor(Date.now() / 1000);
        }
    }

    function prepareBeginnersConditionals(form) {
        const block = form.querySelector("[data-aa-beginners-if-yes]");

        if (!block) {
            return;
        }

        const update = () => {
            const selected = form.querySelector('input[name="confirmation"]:checked')?.value;
            const shouldShow = selected === "yes";

            block.hidden = !shouldShow;

            block
                .querySelectorAll('input[name="roster_length"]')
                .forEach((input) => {
                    input.required = shouldShow;

                    if (!shouldShow) {
                        input.checked = false;
                    }
                });
        };

        form
            .querySelectorAll('input[name="confirmation"]')
            .forEach((input) => input.addEventListener("change", update));

        update();
    }

    forms.forEach((form) => {
        prepareOpenedAt(form);
        prepareBeginnersConditionals(form);

        if (!form.classList.contains("aa-form--modal")) {
            renderTurnstile(form);
        }

        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            if (!form.reportValidity()) {
                return;
            }

            clearStatus(form);

            const turnstileToken = getTurnstileToken(form);

            if (isTurnstileEnabled(form) && !turnstileToken) {
                showStatus(
                    form,
                    "Please complete the verification before sending.",
                    "is-error"
                );
                return;
            }

            const submitButton = form.querySelector("[data-aa-form-submit]");
            const originalText = submitButton?.textContent || "Submit";

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = "Sending...";
            }

            const formData = new FormData(form);

            if (isTurnstileEnabled(form)) {
                formData.set("cf-turnstile-response", turnstileToken);
            }

            formData.append("action", "aa_forms_submit");
            formData.append("nonce", window.aaForms.nonce);

            try {
                const response = await fetch(window.aaForms.ajaxUrl, {
                    method: "POST",
                    credentials: "same-origin",
                    body: formData,
                });

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.data?.message || "Your message could not be sent.");
                }

                const contactRecipient = form.querySelector("[data-aa-contact-recipient]");
                const preservedRecipient = contactRecipient?.value || "";

                showStatus(form, result.data.message, "is-success");
                form.reset();

                if (contactRecipient && preservedRecipient) {
                    contactRecipient.value = preservedRecipient;
                }

                prepareOpenedAt(form);
                prepareBeginnersConditionals(form);

                if (submitButton) {
                    submitButton.textContent = "Sent";
                }
            } catch (error) {
                showStatus(form, error.message, "is-error");

                if (submitButton) {
                    submitButton.textContent = originalText;
                }
            } finally {
                resetTurnstile(form);

                if (submitButton) {
                    submitButton.disabled = false;

                    if (!form.querySelector("[data-aa-form-status]")?.classList.contains("is-success")) {
                        submitButton.textContent = originalText;
                    }
                }
            }
        });
    });

    document.querySelectorAll("[data-aa-form-scope]").forEach((scope) => {
        const modal = scope.querySelector("[data-aa-contact-modal]");
        const form = scope.querySelector("[data-aa-form]");

        if (!modal || !form) {
            return;
        }

        const title = scope.querySelector("[data-aa-contact-title]");
        const recipientInput = scope.querySelector("[data-aa-contact-recipient]");
        const openedAtInput = scope.querySelector("[data-aa-form-opened-at]");
        let previouslyFocusedElement = null;

        function openModal(button) {
            const recipient = button.dataset.recipient;
            const recipientName = button.dataset.recipientName;

            previouslyFocusedElement = document.activeElement;
            recipientInput.value = recipient;
            openedAtInput.value = Math.floor(Date.now() / 1000);

            if (title) {
                title.textContent = `Contact ${recipientName}`;
            }

            clearStatus(form);
            modal.classList.add("is-open");
            modal.setAttribute("aria-hidden", "false");
            document.documentElement.classList.add("aa-contact-modal-open");
            document.body.classList.add("aa-contact-modal-open");
            renderTurnstile(form);

            const firstInput = form.querySelector('input:not([type="hidden"]):not([tabindex="-1"])');

            if (firstInput) {
                firstInput.focus();
            }
        }

        function closeModal() {
            modal.classList.remove("is-open");
            modal.setAttribute("aria-hidden", "true");
            document.documentElement.classList.remove("aa-contact-modal-open");
            document.body.classList.remove("aa-contact-modal-open");
            clearStatus(form);
            form.reset();
            resetTurnstile(form);

            if (previouslyFocusedElement) {
                previouslyFocusedElement.focus();
            }
        }

        scope
            .querySelectorAll("[data-aa-contact-open]")
            .forEach((button) => {
                button.addEventListener("click", () => openModal(button));
            });

        scope
            .querySelectorAll("[data-aa-contact-close]")
            .forEach((button) => button.addEventListener("click", closeModal));

        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape" && modal.classList.contains("is-open")) {
                closeModal();
            }
        });
    });
});
