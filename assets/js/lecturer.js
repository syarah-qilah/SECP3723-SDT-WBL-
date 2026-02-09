document.addEventListener("DOMContentLoaded", function() {
    //Each student detail
    const modal = document.getElementById('studentDetailModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;
            
            // Extract info from data-* attributes
            const name = button.getAttribute('data-name');
            const id = button.getAttribute('data-id');
            const email = button.getAttribute('data-email');
            const dept = button.getAttribute('data-dept');

            // Update the modal's content
            document.getElementById('m-name').textContent = name;
            document.getElementById('m-id-top').textContent = id;
            document.getElementById('m-id-main').textContent = id;
            document.getElementById('m-email').textContent = email;
            document.getElementById('m-dept').textContent = dept;
        });
    }

    //profile update 
    const editBtn = document.getElementById("btn-edit-profile");
    const cancelBtn = document.getElementById("btn-cancel");
    const actionToolbar = document.getElementById("editActions");
    const editableFields = document.querySelectorAll(".editable-field");

    if (editBtn && actionToolbar) {
        // 1. CLICK EDIT
        editBtn.addEventListener("click", function(e) {
            e.preventDefault();
            console.log("Edit Mode Active"); // Debug check
            
            // Toggle Button Visibility
            editBtn.style.display = "none";
            actionToolbar.style.setAttribute("style", "display: flex !important");
            
            // Unlock Fields
            editableFields.forEach(field => {
                field.removeAttribute("readonly");
                field.classList.remove("locked-view");
                field.classList.add("is-editing");
            });
        });

        // 2. CLICK CANCEL
        cancelBtn.addEventListener("click", function(e) {
            e.preventDefault();
            
            // Toggle Button Visibility
            editBtn.style.display = "inline-flex";
            actionToolbar.style.display = "none";
            
            // Lock Fields
            editableFields.forEach(field => {
                field.setAttribute("readonly", true);
                field.classList.add("locked-view");
                field.classList.remove("is-editing");
            });
        });
    }
});