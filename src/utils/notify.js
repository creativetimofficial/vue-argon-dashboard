import Swal from 'sweetalert2'

// Detect if current theme is dark
const isDark = () => document.body.classList.contains('dark-version')

const getThemeColors = () => {
  const dark = isDark()
  return {
    background: dark ? '#1e2a3a' : '#ffffff',
    color: dark ? '#e2e8f0' : '#2d3748',
    borderColor: dark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
    confirmBg: '#2dce89',        // Argon Green
    confirmHover: '#26af72',
    cancelBg: dark ? '#344055' : '#f0f4f8',
    cancelColor: dark ? '#a0aec0' : '#6c757d',
  }
}

const notify = (icon, title, text = '', timer = 3000) => {
  const dark = isDark()
  Swal.fire({
    icon: icon,
    title: title,
    text: text,
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: timer,
    timerProgressBar: true,
    background: dark ? '#1e2a3a' : '#fff',
    color: dark ? '#e2e8f0' : '#2d3748',
    iconColor: icon === 'success' ? '#2dce89' : (icon === 'error' ? '#f5365c' : (icon === 'warning' ? '#fb6340' : '#11cdef')),
    customClass: {
      popup: 'colored-toast shadow-lg rounded-3',
      title: 'text-sm font-weight-bold',
      content: 'text-xs'
    },
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })
}

export const confirm = async (title, text = '', icon = 'question', confirmButtonText = 'Ya, lanjutkan') => {
  const c = getThemeColors()
  const result = await Swal.fire({
    title: title,
    text: text,
    icon: icon,
    background: c.background,
    color: c.color,
    showCancelButton: true,
    confirmButtonText: `<i class="fas fa-check me-1"></i> ${confirmButtonText}`,
    cancelButtonText: `<i class="fas fa-times me-1"></i> Batal`,
    buttonsStyling: false,
    reverseButtons: false,
    focusCancel: true,
    customClass: {
      popup: 'swal-custom-popup',
      title: 'swal-custom-title',
      htmlContainer: 'swal-custom-text',
      confirmButton: 'swal-btn-confirm',
      cancelButton: 'swal-btn-cancel',
      actions: 'swal-custom-actions',
      icon: 'swal-custom-icon',
    },
    showClass: {
      popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
      popup: 'animate__animated animate__fadeOutUp animate__faster'
    }
  })
  return result.isConfirmed
}

export default notify
