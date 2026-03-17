import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';
import Components from 'unplugin-vue-components/vite';
import AutoImport from 'unplugin-auto-import/vite';
import { fileURLToPath, URL } from 'node:url';
import path from 'path';

// Custom Vite plugin to route requests to the correct HTML entry point.
// 1. Any path on a tenant subdomain (e.g., irvan1.localhost:5173) → isp-admin.html (Vuetify)
// 2. /isp-admin/* on the main domain → isp-admin.html (Vuetify)
// 3. Everything else → index.html (Argon / Bootstrap)
function rewriteIspAdmin() {
  return {
    name: 'rewrite-isp-admin',
    configureServer(server) {
      server.middlewares.use((req, res, next) => {
        const host = req.headers.host || '';
        const url = req.url || '';
        const isTenantSubdomain = /^[a-zA-Z0-9-]+\.localhost(:\d+)?$/.test(host);

        // Skip: files with extensions (.js, .css, .vue, .png …)
        // Skip: Vite internal URLs (/@vite/, /@id/, /@fs/, /__vite_ping, etc.)
        // Skip: node_modules requests
        const isStaticFile = url.includes('.');
        const isViteInternal = url.startsWith('/@') || url.startsWith('/__') || url.includes('@id');
        const shouldSkip = isStaticFile || isViteInternal;

        if (!shouldSkip && (isTenantSubdomain || url.startsWith('/isp-admin'))) {
          req.url = '/isp-admin.html';
        }
        next();
      });
    }
  };
}

const sneatBase = path.resolve(__dirname, 'sneat-vuetify-vuejs-admin-template-free/javascript-version/src');

export default defineConfig({
  plugins: [
    rewriteIspAdmin(),
    vue(),
    vuetify({ 
      autoImport: true,
      styles: {
        configFile: 'sneat-vuetify-vuejs-admin-template-free/javascript-version/src/assets/styles/variables/_vuetify.scss',
      }
    }),
    Components({
      dirs: ['src/components'],
      dts: false,
    }),
    AutoImport({
      imports: ['vue', 'vue-router', 'pinia', '@vueuse/core', '@vueuse/math'],
      dts: false,
      eslintrc: { enabled: true },
    }),
  ],

  server: {
    host: true, // Needed for subdomain/multi-tenant testing
    port: 8080,
    strictPort: true,
    // allowedHosts is Vite 6+, leaving for future compatibility
    allowedHosts: 'all',
    cors: true,
  },

  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler',
      },
      sass: {
        api: 'modern-compiler',
      }
    }
  },

  resolve: {
    extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json', '.vue'],
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      '~': fileURLToPath(new URL('./node_modules', import.meta.url)),

      // ── Sneat template path aliases ──────────────────────────────────────
      '@core':        path.join(sneatBase, '@core'),
      '@layouts':     path.join(sneatBase, '@layouts'),
      '@images':      path.join(sneatBase, 'assets/images'),
      '@styles':      path.join(sneatBase, 'assets/styles'),
      '@themeConfig': path.join(sneatBase, 'themeConfig.js'),
      '@configured-variables': path.join(sneatBase, 'assets/styles/variables/_template.scss'),
    },
  },

  build: {
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'index.html'),
        isp:  path.resolve(__dirname, 'isp-admin.html'),
      },
    },
  },

  optimizeDeps: {
    exclude: ['vuetify'],
    entries: ['./src/**/*.vue'],
  },
});
