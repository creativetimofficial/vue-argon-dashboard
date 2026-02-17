import { computed, onMounted, watch } from 'vue';
import { useStore } from 'vuex';

/**
 * Composable for theme management
 * @param {boolean} isPublic - Whether to fetch public theme (for unauthenticated pages)
 */
export function useTheme(isPublic = false) {
  const store = useStore();
  
  const theme = computed(() => store.state.theme.theme);
  const cssVars = computed(() => store.getters['theme/cssVariables']);
  const loading = computed(() => store.state.theme.loading);
  const logoUrl = computed(() => store.getters['theme/logoUrl']);
  const logoDarkUrl = computed(() => store.getters['theme/logoDarkUrl']);
  const companyName = computed(() => store.getters['theme/companyName']);
  
  const loadTheme = async () => {
    try {
      if (isPublic) {
        await store.dispatch('theme/fetchPublicTheme');
      } else {
        await store.dispatch('theme/fetchActiveTheme');
      }
    } catch (error) {
      console.error('Failed to load theme:', error);
    }
  };
  
  const applyTheme = () => {
    const root = document.documentElement;
    Object.entries(cssVars.value).forEach(([key, value]) => {
      root.style.setProperty(key, value);
    });
    
    // Apply font family
    if (theme.value?.font_family) {
      loadGoogleFont(theme.value.font_family);
    }
  };
  
  const loadGoogleFont = (fontName) => {
    const linkId = 'google-font-theme';
    let link = document.getElementById(linkId);
    
    if (!link) {
      link = document.createElement('link');
      link.id = linkId;
      link.rel = 'stylesheet';
      document.head.appendChild(link);
    }
    
    link.href = `https://fonts.googleapis.com/css2?family=${fontName.replace(/ /g, '+')}:wght@300;400;500;600;700&display=swap`;
  };
  
  // Load theme on mount
  onMounted(async () => {
    await loadTheme();
    applyTheme();
  });
  
  // Watch for theme changes and reapply
  watch(theme, () => {
    if (theme.value) {
      applyTheme();
    }
  }, { deep: true });
  
  return {
    theme,
    cssVars,
    loading,
    logoUrl,
    logoDarkUrl,
    companyName,
    loadTheme,
    applyTheme,
  };
}
