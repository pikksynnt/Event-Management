/**
 * Client-side script for Modal and Interactions
 */

document.addEventListener('DOMContentLoaded', function () {
  const openRejectBtn = document.getElementById('btn-open-reject');
  const closeRejectBtn = document.getElementById('btn-close-reject');
  const rejectModal = document.getElementById('reject-modal');
  const rejectForm = document.getElementById('reject-form');
  const rejectionReasonInput = document.getElementById('rejection_reason');

  if (openRejectBtn && rejectModal) {
    openRejectBtn.addEventListener('click', function () {
      rejectModal.classList.add('is-active');
      if (rejectionReasonInput) {
        rejectionReasonInput.focus();
      }
    });
  }

  function closeModal() {
    if (rejectModal) {
      rejectModal.classList.remove('is-active');
    }
  }

  if (closeRejectBtn) {
    closeRejectBtn.addEventListener('click', closeModal);
  }

  if (rejectModal) {
    rejectModal.addEventListener('click', function (e) {
      if (e.target === rejectModal) {
        closeModal();
      }
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && rejectModal && rejectModal.classList.contains('is-active')) {
      closeModal();
    }
  });

  if (rejectForm) {
    rejectForm.addEventListener('submit', function (e) {
      if (rejectionReasonInput && !rejectionReasonInput.value.trim()) {
        e.preventDefault();
        alert('Alasan penolakan wajib diisi.');
        rejectionReasonInput.focus();
      }
    });
  }
});
