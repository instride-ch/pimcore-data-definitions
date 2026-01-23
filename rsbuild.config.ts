/**
 * RSBuild Configuration for Data Definitions Studio Plugin
 */

import { defineConfig } from '@rsbuild/core'
import { pluginReact } from '@rsbuild/plugin-react'
import { pluginSvgr } from '@rsbuild/plugin-svgr'
import { pluginModuleFederation } from '@module-federation/rsbuild-plugin'
import path from 'path'
import fs from 'fs'
import { v4 } from 'uuid'

const buildId = v4()
const bundlePrefix = 'datadefinitions'
const studioAssetsPath = path.resolve(__dirname, 'src/DataDefinitionsBundle/Resources/assets/pimcore-studio')
const buildPath = path.resolve(__dirname, 'src/DataDefinitionsBundle/Resources/public/studio', buildId)
const entryFile = path.resolve(studioAssetsPath, 'src/main.ts')

// Clean old build directories
const studioPath = path.resolve(__dirname, 'src/DataDefinitionsBundle/Resources/public/studio')
if (fs.existsSync(studioPath)) {
  fs.readdirSync(studioPath).forEach((file) => {
    const filePath = path.resolve(studioPath, file)
    if (fs.statSync(filePath).isDirectory()) {
      fs.rmSync(filePath, { recursive: true, force: true })
    }
  })
}

// Ensure build directory exists
if (!fs.existsSync(buildPath)) {
  fs.mkdirSync(buildPath, { recursive: true })
}

let nodeEnv = process.env.NODE_ENV
let env: 'development' | 'production' = 'production'

const isDevServer = nodeEnv === 'dev-server'
if (nodeEnv !== 'production') {
  env = 'development'
}

const devPort = 3050

export default defineConfig({
  mode: env,
  root: studioAssetsPath,
  server: {
    port: devPort,
    publicDir: {
      copyOnBuild: false
    }
  },
  dev: {
    ...(!isDevServer ? { assetPrefix: `/bundles/${bundlePrefix}/studio/${buildId}` } : {}),
    client: {
      host: 'localhost',
      port: devPort,
      protocol: 'ws'
    },
    hmr: true,
  },
  source: {
    entry: {
      main: entryFile
    },
    decorators: {
      version: 'legacy'
    }
  },
  resolve: {
    alias: {
      '@DataDefinitions': path.resolve(studioAssetsPath, 'src'),
      '@DataDefinitions/assets': path.resolve(studioAssetsPath, 'src/assets')
    }
  },
  output: {
    manifest: true,
    assetPrefix: `/bundles/${bundlePrefix}/studio/${buildId}`,
    distPath: {
      root: buildPath
    }
  },
  tools: {
    bundlerChain: (chain, { env }) => {
      chain.output.uniqueName(bundlePrefix)
    }
  },
  plugins: [
    pluginReact(),
    pluginSvgr({
      svgrOptions: {
        icon: true,
        typescript: true
      }
    }),
    pluginModuleFederation({
      name: bundlePrefix,
      filename: 'static/js/remoteEntry.js',
      exposes: {
        '.': entryFile
      },
      dts: false,
      remotes: {
        '@pimcore/studio-ui-bundle': `promise new Promise(resolve => {
          const studioUIBundleRemoteUrl = window.StudioUIBundleRemoteUrl
          const script = document.createElement('script')

          let hasScript = false;

          document.querySelectorAll('script').forEach((el) => {
            const elPathname = el.src.replace(/https?:\\/\\/[^/]+/, '')
            const studioUIBundleRemoteUrlPathname = studioUIBundleRemoteUrl.replace(/https?:\\/\\/[^/]+/, '')

            if (elPathname === studioUIBundleRemoteUrlPathname) {
              hasScript = true;
              return;
            }
          })

          if (hasScript) {
            resolve({
              get: (request) => window['pimcore_studio_ui_bundle'].get(request),
              init: (...arg) => {
                try {
                  return window['pimcore_studio_ui_bundle'].init(...arg)
                } catch(e) {
                  console.log('remote container already initialized')
                }
              }
            })
            return
          }

          script.src = studioUIBundleRemoteUrl
          script.onload = () => {
            const proxy = {
              get: (request) => window['pimcore_studio_ui_bundle'].get(request),
              init: (...arg) => {
                try {
                  return window['pimcore_studio_ui_bundle'].init(...arg)
                } catch(e) {
                  console.log('remote container already initialized')
                }
              }
            }
            resolve(proxy)
          }
          document.head.appendChild(script);
        })
        `
      },
      shared: {
        react: {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        'react-dom': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        'react/jsx-runtime': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        'react/jsx-dev-runtime': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        'react-i18next': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        'i18next': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        '@emotion/react': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        '@emotion/styled': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        antd: {
          singleton: true,
          eager: false,
          requiredVersion: false
        },
        '@reduxjs/toolkit': {
          singleton: true,
          eager: false,
          requiredVersion: false
        },
        'react-redux': {
          singleton: true,
          eager: false,
          requiredVersion: false
        },
        immer: {
          singleton: true,
          eager: false,
          requiredVersion: false
        },
        zustand: {
          singleton: true,
          eager: false,
          requiredVersion: false
        }
      }
    })
  ]
})
