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

    let previouslyFocusedElement = null;


    function openModal(button) {
        const recipient = button.dataset.recipient;
        const recipientName = button.dataset.recipientName;

        previouslyFocusedElement = document.activeElement;

        recipientInput.value = recipient;
        openedAtInput.value = Math.floor(Date.now() / 1000);

        title.textContent = `Contact ${recipientName}`;

        status.textContent = "";
        status.classList.remove(
            "is-success",
            "is-error"
        );

        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");

        document.body.classList.add("aa-contact-modal-open");

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

        status.textContent = "";
        status.classList.remove(
            "is-success",
            "is-error"
        );

        form.reset();

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

        submitButton.disabled = true;
        submitButton.textContent = "Sending...";

        status.textContent = "";
        status.classList.remove(
            "is-success",
            "is-error"
        );

        const formData = new FormData(form);

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

            status.textContent = result.data.message;
            status.classList.add("is-success");

            /*
             * Preserve recipient/opened_at before reset if desired,
             * but after success we don't need them anymore.
             */
            form.reset();

            submitButton.textContent = "Sent";

        } catch (error) {
            status.textContent = error.message;
            status.classList.add("is-error");

        } finally {
            submitButton.disabled = false;

            if (
                !status.classList.contains("is-success")
            ) {
                submitButton.textContent = "Send message";
            }
        }
    });
});