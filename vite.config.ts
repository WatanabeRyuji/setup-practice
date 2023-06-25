import { defineConfig, UserConfigExport, ConfigEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import react from '@vitejs/plugin-react'

export default ({ command, mode }: ConfigEnv): UserConfigExport => {
    const isProduction = command === 'build'

    return defineConfig({
        // ビルドモードとサーブモードの両方に共通の設定
        base: '/',
        plugins: [
            laravel({
                input: 'resources/js/app.tsx',
                refresh: true
            }),
            react({
                jsxImportSource: '@emotion/react'
            })
        ],

        // ビルドモードに特有の設定
        // build: isProduction
        //   ? {
        //       minify: true,
        //       outDir: 'dist'
        //     }
        //   : undefined,

        // サーブモードに特有の設定
        server: {
            host: '0.0.0.0',
            port: 5173
        }
    })
}

