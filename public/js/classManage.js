document.addEventListener("DOMContentLoaded", function () {
    let modal = document.getElementById("attendanceModal"); // Get the modal
    if (!modal) {
        console.error("Error: attendanceModal not found in the document.");
        return; // Stop execution if modal is missing
    }

    // Use event delegation to handle dynamically loaded elements
    document.body.addEventListener("click", function (event) {
        let viewBtn = event.target.closest(".view-btn");
        if (viewBtn) {
            event.preventDefault();
            console.log("Button Clicked:", viewBtn); // Debugging
            modal.style.display = "flex"; // Open modal
        }
    });

    // Close modal button
    let closeAttendance = document.getElementById("closeAttendance");
    if (closeAttendance) {
        closeAttendance.addEventListener("click", function () {
            modal.style.display = "none";
        });
    }

    // Close modal when clicking outside
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Edit Class Modal Handling
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            // Get modal
            let modal = document.getElementById("editClassModal");

            // Set form fields with existing class data
            document.getElementById("editClassId").value = this.getAttribute("data-id");
            document.getElementById("editClassName").value = this.getAttribute("data-name");
            document.getElementById("editClassSubject").value = this.getAttribute("data-subject");
            document.getElementById("editClassCode").value = this.getAttribute("data-code");
            document.getElementById("editClassCapacity").value = this.getAttribute("data-capacity");
            document.getElementById("editFirstDay").value = this.getAttribute("data-firstday");
            document.getElementById("editLastDay").value = this.getAttribute("data-lastday");
            document.getElementById("editStartTime").value = this.getAttribute("data-start");
            document.getElementById("editEndTime").value = this.getAttribute("data-end");
            document.getElementById("editClassStatus").value = this.getAttribute("data-status");
            event.preventDefault();
            // Show modal
            modal.style.display = "flex";
        });
    });

    // Close modal when clicking the close button
    document.getElementById("closeEditClass").addEventListener("click", function () {
        document.getElementById("editClassModal").style.display = "none";
    });

    // Close modal when clicking outside of it
    window.onclick = function (event) {
        let modal = document.getElementById("editClassModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

});


