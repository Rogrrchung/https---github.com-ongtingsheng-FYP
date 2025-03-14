function filterStudents() {
    let selectedProgramme = document.getElementById("programmeFilter").value;
    let rows = document.querySelectorAll("#studentList tr");

    rows.forEach(row => {
        if (selectedProgramme === "all" || row.dataset.programme === selectedProgramme) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("addStudentModal");
    let closeModal = document.getElementById("closeAddStudent");
    let formClassId = document.getElementById("formClassId");

    // Add event listeners to all "Add Student" buttons
    document.querySelectorAll(".add-student-btn").forEach(button => {
        button.addEventListener("click", function () {
            let classId = this.getAttribute("data-class-id"); // Get class_id from the button
            console.log("Class ID:", classId); // Debugging
            formClassId.value = classId; // Set the class_id in the hidden input
            modal.style.display = "block"; // Show the modal
        });
    });

    // Add event listener to the form submission
    document.querySelector("form").addEventListener("submit", function (event) {
        console.log("Form submitted. Class ID:", formClassId.value); // Debugging: Check if class_id is set
    });

    // Close the modal
    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
    });
});