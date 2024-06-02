// modalFunctions.js
function openConfirmationModal(userId) {
    document.getElementById('userIdToDelete').value = userId;
    document.getElementById('confirmationModal').style.display = 'block';
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
}

function submitDelete() {
    var userId = document.getElementById('userIdToDelete').value;
    window.location.href = 'user_deleteProcess.php?id_pg=' + userId;
}
