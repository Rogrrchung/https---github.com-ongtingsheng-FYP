
document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll(".pin-input");
    const form = document.querySelector("form");
    const errorMessage = document.getElementById("error-message");

    // Auto-focus to next input & restrict to numbers
    inputs.forEach((input, index) => {
        input.addEventListener("input", (e) => {
            if (!/^\d$/.test(e.target.value)) {
                e.target.value = ""; // Clear invalid input
                showError("Only numbers are allowed!");
                return;
            }

            if (index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    // Form validation before submission
    form.addEventListener("submit", function (e) {
        let pin = "";
        inputs.forEach(input => pin += input.value);

        if (pin.length !== 6) {
            e.preventDefault();
            showError("Please enter all 6 digits.");
        }

        document.getElementById("full-pin").value = pin; // Set hidden field value
    });

    // Show error message
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = "block";

        setTimeout(() => {
            errorMessage.style.display = "none";
        }, 3000);
    }
});

