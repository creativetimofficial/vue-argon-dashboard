import checkboxChecked from '@images/svg/checkbox-checked.svg'
import checkboxIndeterminate from '@images/svg/checkbox-indeterminate.svg'
import checkboxUnchecked from '@images/svg/checkbox-unchecked.svg'
import radioChecked from '@images/svg/radio-checked.svg'
import radioUnchecked from '@images/svg/radio-unchecked.svg'

const customIcons = {
  'mdi-checkbox-blank-outline': checkboxUnchecked,
  'mdi-checkbox-marked': checkboxChecked,
  'mdi-minus-box': checkboxIndeterminate,
  'mdi-radiobox-marked': radioChecked,
  'mdi-radiobox-blank': radioUnchecked,
}

const aliases = {
  calendar: 'bx-calendar',
  collapse: 'bx-chevron-up',
  complete: 'bx-check',
  cancel: 'bx-x',
  close: 'bx-x',
  delete: 'bx-bxs-x-circle',
  clear: 'bx-x-circle',
  success: 'bx-check-circle',
  info: 'bx-info-circle',
  warning: 'bx-error',
  error: 'bx-error-circle',
  prev: 'bx-chevron-left',
  ratingEmpty: 'bx-star',
  ratingFull: 'bx-bxs-star',
  ratingHalf: 'bx-bxs-star-half',
  next: 'bx-chevron-right',
  delimiter: 'bx-circle',
  sort: 'bx-up-arrow-alt',
  expand: 'bx-chevron-down',
  menu: 'bx-menu',
  subgroup: 'bx-caret-down',
  dropdown: 'bx-chevron-down',
  edit: 'bx-pencil',
  loading: 'bx-refresh',
  first: 'bx-skip-previous',
  last: 'bx-skip-next',
  unfold: 'bx-move-vertical',
  file: 'bx-paperclip',
  plus: 'bx-plus',
  minus: 'bx-minus',
  sortAsc: 'bx-up-arrow-alt',
  sortDesc: 'bx-down-arrow-alt',
}

export const iconify = {
  component: props => {
    // Load custom SVG directly instead of going through icon component
    if (typeof props.icon === 'string') {
      const iconComponent = customIcons[props.icon]
      if (iconComponent)
        return h(iconComponent)
    }
    
    return h(props.tag, {
      ...props,

      // As we are using class based icons
      class: [props.icon],

      // Remove used props from DOM rendering
      tag: undefined,
      icon: undefined,
    })
  },
}
export const icons = {
  defaultSet: 'iconify',
  aliases,
  sets: {
    iconify,
  },
}
