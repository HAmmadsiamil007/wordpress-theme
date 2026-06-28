import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig( {
    base: '/wp-content/themes/soleorigine-theme/dist/',

    resolve: {
        alias: {
            '@js': path.resolve( __dirname, 'js' ),
            '@css': path.resolve( __dirname, 'css' ),
        },
    },

    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                'custom': path.resolve( __dirname, 'js/custom.js' ),
                'navigation': path.resolve( __dirname, 'js/navigation.js' ),
                'woocommerce': path.resolve( __dirname, 'css/woocommerce.css' ),
                'responsive': path.resolve( __dirname, 'css/responsive.css' ),
                'admin': path.resolve( __dirname, 'css/admin.css' ),
            },
        },
    },

    server: {
        port: 5173,
        strictPort: true,
        cors: true,
        allowedHosts: [ 'soleorigine.local' ],
    },
} );
