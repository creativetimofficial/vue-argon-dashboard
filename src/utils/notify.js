import Swal from 'sweetalert2'

const notify = (icon, title, text = '', timer = 3000) => {
  Swal.fire({
    icon: icon,
    title: title,
    text: text,
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: timer,
    timerProgressBar: true,
    background: '#fff',
    iconColor: icon === 'success' ? '#2dce89' : (icon === 'error' ? '#f5365c' : '#11cdef'),
    customClass: {
      popup: 'colored-toast shadow-lg',
      title: 'text-sm font-weight-bold',
      content: 'text-xs'
    },
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })
}

export const confirm = async (title, text, icon = 'warning', confirmButtonText = 'Yes, confirm!') => {
  const result = await Swal.fire({
    title: title,
    text: text,
    icon: icon,
    showCancelButton: true,
    confirmButtonColor: '#5e72e4', // Argon Primary
    cancelButtonColor: '#f5365c', // Argon Danger
    confirmButtonText: confirmButtonText
  })
  return result.isConfirmed
}

export default notify
