import { defineConfig } from 'vite';
import fs from 'fs';
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { wordpressPlugin, wordpressThemeJson } from '@roots/vite-plugin';

export default defineConfig({
  server: {
    base: '/wp-content/themes/ejsage/public/',
    host: 'allianze.local',
    port: 5173,
    https: {
      key: fs.readFileSync('C:/Users/ejuk/AppData/Roaming/Local/run/router/nginx/certs/allianze.local.key'),
      cert: fs.readFileSync('C:/Users/ejuk/AppData/Roaming/Local/run/router/nginx/certs/allianze.local.crt'),
    },
    origin: 'https://allianze.local:5173',
    cors: true,
  },
  plugins: [
    tailwindcss(),
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/editor.css',
        'resources/js/editor.js',
      ],
      refresh: true,
    }),

    ViteImageOptimizer({
      png: {
          quality: 80,
      },
      jpeg: {
          quality: 80,
      },
      jpg: {
          quality: 80,
      },
      webp: {
          quality: 80,
      },
  }),

    wordpressPlugin(),

    // Generate the theme.json file in the public/build/assets directory
    // based on the Tailwind config and the theme.json file from base theme folder
    wordpressThemeJson({
      disableTailwindColors: false,
      disableTailwindFonts: false,
      disableTailwindFontSizes: false,
    }),
  ],
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles': '/resources/css',
      '@fonts': '/resources/fonts',
      '@images': '/resources/images',
    },
  },
});
