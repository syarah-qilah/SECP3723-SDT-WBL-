document.addEventListener("DOMContentLoaded", function() {
    //---Register Course Tab
    const regButtons = document.querySelectorAll('.btn-register');
    const regModal = new bootstrap.Modal(document.getElementById('regModal'));
    const modalCourseName = document.getElementById('modal-course-name');

    regButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const courseName = this.getAttribute('data-course');
            
            // 1. Show Loading State on Button
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            this.style.pointerEvents = 'none';

            // 2. Simulate Backend Check (1.5 seconds)
            setTimeout(() => {
                // Update Modal Text
                modalCourseName.innerText = courseName;
                
                // Show Success Modal
                regModal.show();

                // Reset Button
                this.innerHTML = originalContent;
                this.style.pointerEvents = 'auto';
                
                // Optionally: Change button to "Pending"
                this.classList.replace('btn-primary', 'btn-outline-secondary');
                this.innerHTML = '<i class="fas fa-clock"></i> Pending Approval';
                this.disabled = true;
            }, 1500);
        });
    });

    
    //courselisthistory 
    const triggerTabList = [].slice.call(document.querySelectorAll('#courseTabs button'));
        triggerTabList.forEach(function (triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl);

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });

    // --- PROFILE EDIT LOGIC ---
    console.log("Student.js is loaded and ready!"); // Debug: Check your browser console (F12)

    const editBtn = document.getElementById("btn-edit-profile");
    
    if (editBtn) {
        const cancelBtn = document.getElementById("btn-cancel");
        const actionToolbar = document.getElementById("editActions");
        const profileForm = document.getElementById("profileForm");
        const editableInputs = document.querySelectorAll(".editable-field");

        // 1. Enable Edit
        editBtn.addEventListener("click", function(e) {
            e.preventDefault(); // Stop any default jumpy behavior
            console.log("Edit clicked");
            
            editBtn.style.display = "none";
            // Use style.setProperty to bypass any potential CSS !important
            actionToolbar.style.setProperty("display", "flex", "important"); 
            
            editableInputs.forEach(input => {
                input.removeAttribute("readonly");
                input.classList.remove("locked-view");
                input.classList.add("is-editing");
            });
            if(editableInputs.length > 0) editableInputs[0].focus();
        });

        // 2. Cancel
        cancelBtn.addEventListener("click", function(e) {
            e.preventDefault();
            editBtn.style.display = "inline-flex";
            actionToolbar.style.setProperty("display", "none", "important");
            
            editableInputs.forEach(input => {
                input.setAttribute("readonly", true);
                input.classList.add("locked-view");
                input.classList.remove("is-editing");
            });
        });

        // 3. Save Logic remains the same...
        profileForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const saveBtn = this.querySelector('button[type="submit"]');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            setTimeout(() => {
                editableInputs.forEach(input => {
                    input.setAttribute("readonly", true);
                    input.classList.add("locked-view");
                    input.classList.remove("is-editing");
                });
                editBtn.style.display = "inline-flex";
                actionToolbar.style.setProperty("display", "none", "important");
                saveBtn.innerHTML = originalText;
                alert("✅ Profile updated successfully!");
            }, 1000);
        });
    }
});