/* ==========================================================================
   1. COURSE MANAGEMENT MODALS
   ========================================================================== */

/* --- Add Course Modal --- */
function openModal() {
    const modal = document.getElementById('addCourseModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal() {
    const modal = document.getElementById('addCourseModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

/* --- Edit Course Modal --- */
function openEditModal(id, code, name, semester, credits, max, lecturer, schedule, desc) {
    const modal = document.getElementById('editCourseModal');
    if (modal) {
        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Populate fields
        document.getElementById('edit_course_id').value = id;
        document.getElementById('edit_course_code').value = code;
        document.getElementById('edit_course_name').value = name;
        document.getElementById('edit_semester').value = semester;
        document.getElementById('edit_credits').value = credits;
        document.getElementById('edit_max_students').value = max;
        document.getElementById('edit_lecturer').value = lecturer;
        document.getElementById('edit_schedule').value = schedule;
        document.getElementById('edit_description').value = desc;
    }
}

function closeEditModal() {
    const modal = document.getElementById('editCourseModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}

/* --- Delete Course Modal --- */
function openDeleteModal(id, name) {
    const modal = document.getElementById('deleteCourseModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Pass data to form
        document.getElementById('delete_course_id').value = id;
        document.getElementById('delete_course_name_display').innerText = name;
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteCourseModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}


/* ==========================================================================
   2. STUDENT MANAGEMENT MODALS
   ========================================================================== */

/* --- Add Student Modal --- */
function openAddStudentModal() {
    const modal = document.getElementById('addStudentModal');
    if(modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        console.error("Error: Modal #addStudentModal not found in HTML");
    }
}

function closeAddStudentModal() {
    const modal = document.getElementById('addStudentModal');
    if(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}
/* =========================================
   4. LECTURER MODAL FUNCTIONS
   ========================================= */

function openLecturerModal() {
    const modal = document.getElementById('addLecturerModal');
    if(modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeLecturerModal() {
    const modal = document.getElementById('addLecturerModal');
    if(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}


/* ==========================================================================
   GLOBAL CLICK HANDLER (Unified)
   ========================================================================== */

// This single function handles closing ANY modal when clicking outside
window.onclick = function(event) {
    const addCourseModal   = document.getElementById('addCourseModal');
    const editCourseModal  = document.getElementById('editCourseModal');
    const deleteCourseModal= document.getElementById('deleteCourseModal');
    const addStudentModal  = document.getElementById('addStudentModal');
    const LecturerModal = document.getElementById('addLecturerModal');
    
    if (event.target == addCourseModal)    closeModal();
    if (event.target == editCourseModal)   closeEditModal();
    if (event.target == deleteCourseModal) closeDeleteModal();
    if (event.target == addStudentModal)   closeAddStudentModal();
    if (event.target == LecturerModal)  closeLecturerModal();
}

