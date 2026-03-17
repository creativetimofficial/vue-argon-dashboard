/**
 * ISP Admin Vuetify Plugin
 * 
 * Based on Sneat template configuration but with two key differences:
 * 1. No @core/scss imports (avoid @configured-variables Sass error)
 * 2. No SVG file imports for icons (avoid InvalidCharacterError from @images path strings)
 *    — uses BoxIcons class-based icons everywhere instead
 */
import { createVuetify } from 'vuetify';
import { VBtn } from 'vuetify/components/VBtn';
import { h } from 'vue';
import 'vuetify/styles';

// Use Sneat's defaults config (safe — no SCSS or SVG imports)
import defaults from '../../sneat-vuetify-vuejs-admin-template-free/javascript-version/src/plugins/vuetify/defaults.js';

// Use Sneat's theme config (safe — only JS objects)
import { themes } from '../../sneat-vuetify-vuejs-admin-template-free/javascript-version/src/plugins/vuetify/theme.js';

// ─── Icon configuration ────────────────────────────────────────────────────
// BoxIcons font requires TWO classes: `bx` (base) + `bx-icon-name` (specific).
// Example: <i class="bx bx-wifi"> — without `bx` base class, glyph = empty square.

const iconify = {
  component: (props) => {
    const iconName = props.icon || '';
    const tag = props.tag || 'i';

    let iconClasses = [];
    if (iconName && /^bx[sl]?-/.test(iconName)) {
      iconClasses = ['bx', iconName];          // → class="bx bx-wifi"
    } else if (iconName) {
      iconClasses = [iconName];                // other icon sets pass-through
    }

    const { tag: _tag, icon: _icon, class: _propsClass, ...rest } = props;
    
    // Merge Vuetify props.class with the parsed Boxicons classes
    const finalClass = [].concat(_propsClass || [], iconClasses);

    return h(tag, { ...rest, class: finalClass });
  },
};

const icons = {
  defaultSet: 'iconify',
  aliases: {
    calendar:      'bx-calendar',
    collapse:      'bx-chevron-up',
    complete:      'bx-check',
    cancel:        'bx-x',
    close:         'bx-x',
    delete:        'bx-x-circle',
    clear:         'bx-x-circle',
    success:       'bx-check-circle',
    info:          'bx-info-circle',
    warning:       'bx-error',
    error:         'bx-error-circle',
    prev:          'bx-chevron-left',
    ratingEmpty:   'bx-star',
    ratingFull:    'bx-bxs-star',
    ratingHalf:    'bx-bxs-star-half',
    next:          'bx-chevron-right',
    delimiter:     'bx-circle',
    sort:          'bx-up-arrow-alt',
    expand:        'bx-chevron-down',
    menu:          'bx-menu',
    subgroup:      'bx-caret-down',
    dropdown:      'bx-chevron-down',
    edit:          'bx-pencil',
    loading:       'bx-refresh',
    first:         'bx-skip-previous',
    last:          'bx-skip-next',
    unfold:        'bx-move-vertical',
    file:          'bx-paperclip',
    plus:          'bx-plus',
    minus:         'bx-minus',
    sortAsc:       'bx-up-arrow-alt',
    sortDesc:      'bx-down-arrow-alt',
    // Checkbox & radio — use BoxIcons instead of SVG components
    checkboxOff:   'bx-square',
    checkboxOn:    'bx-checkbox-checked',
    checkboxIndeterminate: 'bx-minus-circle',
    radioOff:      'bx-radio-circle',
    radioOn:       'bx-radio-circle-marked',
  },
  sets: { iconify },
};
// ──────────────────────────────────────────────────────────────────────────

export default function (app) {
  const savedTheme = localStorage.getItem('isp_admin_theme') || 'light';
  let initialTheme = savedTheme;
  
  // Resolve 'system' to actual theme name for Vuetify initialization
  if (savedTheme === 'system') {
    initialTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  const vuetify = createVuetify({
    aliases: {
      IconBtn: VBtn,
    },
    defaults,
    icons,
    theme: {
      defaultTheme: initialTheme,
      themes,
    },
  });

  app.use(vuetify);
}
