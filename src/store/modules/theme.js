import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export default {
  namespaced: true,
  
  state: {
    theme: null,
    loading: false,
    error: null,
  },
  
  mutations: {
    SET_THEME(state, theme) {
      state.theme = theme;
      console.log('Theme set in store:', theme); // Debug log
    },
    SET_LOADING(state, loading) {
      state.loading = loading;
    },
    SET_ERROR(state, error) {
      state.error = error;
    },
  },
  
  actions: {
    async fetchPublicTheme({ commit }) {
      commit('SET_LOADING', true);
      try {
        const response = await axios.get(`${API_URL}/theme/public`);
        console.log('Public theme fetched:', response.data); // Debug log
        commit('SET_THEME', response.data);
        return response.data;
      } catch (error) {
        console.error('Failed to fetch public theme:', error);
        commit('SET_ERROR', error.message);
        // Return default theme on error
        return null;
      } finally {
        commit('SET_LOADING', false);
      }
    },
    
    async fetchActiveTheme({ commit }) {
      commit('SET_LOADING', true);
      try {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (!token) {
          // No token, fallback to public theme
          const response = await axios.get(`${API_URL}/theme/public`);
          console.log('Active theme (fallback to public):', response.data); // Debug log
          commit('SET_THEME', response.data);
          return response.data;
        }
        
        const response = await axios.get(`${API_URL}/super-admin/isp-themes/active`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        console.log('Active theme fetched:', response.data); // Debug log
        commit('SET_THEME', response.data);
        return response.data;
      } catch (error) {
        console.error('Failed to fetch active theme:', error);
        commit('SET_ERROR', error.message);
        // Fallback to public theme on error
        try {
          const response = await axios.get(`${API_URL}/theme/public`);
          commit('SET_THEME', response.data);
          return response.data;
        } catch (fallbackError) {
          console.error('Failed to fetch fallback theme:', fallbackError);
          return null;
        }
      } finally {
        commit('SET_LOADING', false);
      }
    },
  },
  
  getters: {
    cssVariables: (state) => {
      if (!state.theme) return {};
      
      const vars = {
        // Use actual database column names (primary_color, not color_primary)
        '--color-primary': state.theme.primary_color || '#2dce89',
        '--color-secondary': state.theme.secondary_color || '#11cdef',
        '--color-accent': state.theme.accent_color || '#5e72e4',
        '--color-success': state.theme.primary_color || '#2dce89',
        '--color-danger': '#f5365c',
        '--color-warning': '#fb6340',
        '--color-info': state.theme.secondary_color || '#11cdef',
        '--button-primary': state.theme.button_primary_color || state.theme.primary_color || '#2dce89',
        '--button-secondary': state.theme.button_secondary_color || state.theme.secondary_color || '#11cdef',
        '--link-color': state.theme.link_color || state.theme.accent_color || '#5e72e4',
        '--text-primary': state.theme.text_primary_color || state.theme.text_color || '#344767',
        '--text-secondary': state.theme.text_secondary_color || '#8392ab',
        '--sidebar-bg': state.theme.sidebar_bg_color || '#ffffff',
        '--navbar-bg': state.theme.navbar_bg_color || '#ffffff',
        '--dashboard-bg': state.theme.dashboard_bg_color || state.theme.background_color || '#f8f9fa',
        '--font-family': state.theme.font_family || 'Open Sans',
      };
      
      console.log('CSS Variables generated:', vars); // Debug log
      return vars;
    },
    
    logoUrl: (state) => {
      if (!state.theme) return null;
      return state.theme.use_custom_logo ? (state.theme.logo_url || state.theme.logo_light) : null;
    },
    
    logoDarkUrl: (state) => {
      if (!state.theme) return null;
      return state.theme.use_custom_logo ? (state.theme.logo_dark_url || state.theme.logo_dark) : null;
    },
    
    companyName: (state) => {
      if (!state.theme) return null;
      return state.theme.company_name || null;
    },
  },
};
