// vite.config.js
import path from "path";
import { defineConfig, loadEnv } from "file:///C:/wamp64/www/projects/GetGo/Logan-Express-care/Logan-Express/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/wamp64/www/projects/GetGo/Logan-Express-care/Logan-Express/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/wamp64/www/projects/GetGo/Logan-Express-care/Logan-Express/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import Components from "file:///C:/wamp64/www/projects/GetGo/Logan-Express-care/Logan-Express/node_modules/unplugin-vue-components/dist/vite.js";
import { AntDesignVueResolver } from "file:///C:/wamp64/www/projects/GetGo/Logan-Express-care/Logan-Express/node_modules/unplugin-vue-components/dist/resolvers.js";
import tailwind from "file:///C:/wamp64/www/projects/GetGo/Logan-Express-care/Logan-Express/node_modules/tailwindcss/lib/index.js";
import autoprefixer from "file:///C:/wamp64/www/projects/GetGo/Logan-Express-care/Logan-Express/node_modules/autoprefixer/lib/autoprefixer.js";
var __vite_injected_original_dirname = "C:\\wamp64\\www\\projects\\GetGo\\Logan-Express-care\\Logan-Express";
var vite_config_default = defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd());
  return {
    css: {
      preprocessorOptions: {
        less: {
          javascriptEnabled: true
          // Enable inline JavaScript in Less
        }
      },
      postcss: {
        plugins: [tailwind(), autoprefixer()]
      }
    },
    plugins: [
      laravel({
        input: ["resources/js/app.js"]
      }),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false
          }
        }
      }),
      Components({
        resolvers: [
          AntDesignVueResolver({
            importStyle: false
            // css in js
          })
        ]
      })
    ],
    resolve: {
      alias: {
        "@": path.resolve(__vite_injected_original_dirname, "./resources/js")
      }
    },
    server: {
      hmr: {
        host: env.VITE_HRM_HOST ?? "localhost"
      }
    }
  };
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFx3YW1wNjRcXFxcd3d3XFxcXHByb2plY3RzXFxcXEdldEdvXFxcXExvZ2FuLUV4cHJlc3MtY2FyZVxcXFxMb2dhbi1FeHByZXNzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJDOlxcXFx3YW1wNjRcXFxcd3d3XFxcXHByb2plY3RzXFxcXEdldEdvXFxcXExvZ2FuLUV4cHJlc3MtY2FyZVxcXFxMb2dhbi1FeHByZXNzXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9DOi93YW1wNjQvd3d3L3Byb2plY3RzL0dldEdvL0xvZ2FuLUV4cHJlc3MtY2FyZS9Mb2dhbi1FeHByZXNzL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHBhdGggZnJvbSBcInBhdGhcIjtcbmltcG9ydCB7IGRlZmluZUNvbmZpZywgbG9hZEVudiB9IGZyb20gXCJ2aXRlXCI7XG5pbXBvcnQgbGFyYXZlbCBmcm9tIFwibGFyYXZlbC12aXRlLXBsdWdpblwiO1xuaW1wb3J0IHZ1ZSBmcm9tIFwiQHZpdGVqcy9wbHVnaW4tdnVlXCI7XG5pbXBvcnQgQ29tcG9uZW50cyBmcm9tIFwidW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZVwiO1xuaW1wb3J0IHsgQW50RGVzaWduVnVlUmVzb2x2ZXIgfSBmcm9tIFwidW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvcmVzb2x2ZXJzXCI7XG5cbmltcG9ydCB0YWlsd2luZCBmcm9tIFwidGFpbHdpbmRjc3NcIjtcbmltcG9ydCBhdXRvcHJlZml4ZXIgZnJvbSBcImF1dG9wcmVmaXhlclwiO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoKHsgbW9kZSB9KSA9PiB7XG4gICAgLy8gTG9hZCAuZW52IGZpbGUgYmFzZWQgb24gY3VycmVudCBtb2RlIChlLmcuLCAuZW52LCAuZW52LmRldmVsb3BtZW50LCBldGMuKVxuICAgIGNvbnN0IGVudiA9IGxvYWRFbnYobW9kZSwgcHJvY2Vzcy5jd2QoKSk7XG5cbiAgICByZXR1cm4ge1xuICAgICAgICBjc3M6IHtcbiAgICAgICAgICAgIHByZXByb2Nlc3Nvck9wdGlvbnM6IHtcbiAgICAgICAgICAgICAgICBsZXNzOiB7XG4gICAgICAgICAgICAgICAgICAgIGphdmFzY3JpcHRFbmFibGVkOiB0cnVlLCAvLyBFbmFibGUgaW5saW5lIEphdmFTY3JpcHQgaW4gTGVzc1xuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgcG9zdGNzczoge1xuICAgICAgICAgICAgICAgIHBsdWdpbnM6IFt0YWlsd2luZCgpLCBhdXRvcHJlZml4ZXIoKV0sXG4gICAgICAgICAgICB9LFxuICAgICAgICB9LFxuICAgICAgICBwbHVnaW5zOiBbXG4gICAgICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgICAgICBpbnB1dDogW1wicmVzb3VyY2VzL2pzL2FwcC5qc1wiXSxcbiAgICAgICAgICAgIH0pLFxuICAgICAgICAgICAgdnVlKHtcbiAgICAgICAgICAgICAgICB0ZW1wbGF0ZToge1xuICAgICAgICAgICAgICAgICAgICB0cmFuc2Zvcm1Bc3NldFVybHM6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGJhc2U6IG51bGwsXG4gICAgICAgICAgICAgICAgICAgICAgICBpbmNsdWRlQWJzb2x1dGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9KSxcbiAgICAgICAgICAgIENvbXBvbmVudHMoe1xuICAgICAgICAgICAgICAgIHJlc29sdmVyczogW1xuICAgICAgICAgICAgICAgICAgICBBbnREZXNpZ25WdWVSZXNvbHZlcih7XG4gICAgICAgICAgICAgICAgICAgICAgICBpbXBvcnRTdHlsZTogZmFsc2UsIC8vIGNzcyBpbiBqc1xuICAgICAgICAgICAgICAgICAgICB9KSxcbiAgICAgICAgICAgICAgICBdLFxuICAgICAgICAgICAgfSksXG4gICAgICAgIF0sXG4gICAgICAgIHJlc29sdmU6IHtcbiAgICAgICAgICAgIGFsaWFzOiB7XG4gICAgICAgICAgICAgICAgXCJAXCI6IHBhdGgucmVzb2x2ZShfX2Rpcm5hbWUsIFwiLi9yZXNvdXJjZXMvanNcIiksXG4gICAgICAgICAgICB9LFxuICAgICAgICB9LFxuICAgICAgICBzZXJ2ZXI6IHtcbiAgICAgICAgICAgIGhtcjoge1xuICAgICAgICAgICAgICAgIGhvc3Q6IGVudi5WSVRFX0hSTV9IT1NUID8/ICdsb2NhbGhvc3QnLFxuICAgICAgICAgICAgfSxcbiAgICAgICAgfSxcbiAgICB9XG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBdVgsT0FBTyxVQUFVO0FBQ3hZLFNBQVMsY0FBYyxlQUFlO0FBQ3RDLE9BQU8sYUFBYTtBQUNwQixPQUFPLFNBQVM7QUFDaEIsT0FBTyxnQkFBZ0I7QUFDdkIsU0FBUyw0QkFBNEI7QUFFckMsT0FBTyxjQUFjO0FBQ3JCLE9BQU8sa0JBQWtCO0FBUnpCLElBQU0sbUNBQW1DO0FBVXpDLElBQU8sc0JBQVEsYUFBYSxDQUFDLEVBQUUsS0FBSyxNQUFNO0FBRXRDLFFBQU0sTUFBTSxRQUFRLE1BQU0sUUFBUSxJQUFJLENBQUM7QUFFdkMsU0FBTztBQUFBLElBQ0gsS0FBSztBQUFBLE1BQ0QscUJBQXFCO0FBQUEsUUFDakIsTUFBTTtBQUFBLFVBQ0YsbUJBQW1CO0FBQUE7QUFBQSxRQUN2QjtBQUFBLE1BQ0o7QUFBQSxNQUNBLFNBQVM7QUFBQSxRQUNMLFNBQVMsQ0FBQyxTQUFTLEdBQUcsYUFBYSxDQUFDO0FBQUEsTUFDeEM7QUFBQSxJQUNKO0FBQUEsSUFDQSxTQUFTO0FBQUEsTUFDTCxRQUFRO0FBQUEsUUFDSixPQUFPLENBQUMscUJBQXFCO0FBQUEsTUFDakMsQ0FBQztBQUFBLE1BQ0QsSUFBSTtBQUFBLFFBQ0EsVUFBVTtBQUFBLFVBQ04sb0JBQW9CO0FBQUEsWUFDaEIsTUFBTTtBQUFBLFlBQ04saUJBQWlCO0FBQUEsVUFDckI7QUFBQSxRQUNKO0FBQUEsTUFDSixDQUFDO0FBQUEsTUFDRCxXQUFXO0FBQUEsUUFDUCxXQUFXO0FBQUEsVUFDUCxxQkFBcUI7QUFBQSxZQUNqQixhQUFhO0FBQUE7QUFBQSxVQUNqQixDQUFDO0FBQUEsUUFDTDtBQUFBLE1BQ0osQ0FBQztBQUFBLElBQ0w7QUFBQSxJQUNBLFNBQVM7QUFBQSxNQUNMLE9BQU87QUFBQSxRQUNILEtBQUssS0FBSyxRQUFRLGtDQUFXLGdCQUFnQjtBQUFBLE1BQ2pEO0FBQUEsSUFDSjtBQUFBLElBQ0EsUUFBUTtBQUFBLE1BQ0osS0FBSztBQUFBLFFBQ0QsTUFBTSxJQUFJLGlCQUFpQjtBQUFBLE1BQy9CO0FBQUEsSUFDSjtBQUFBLEVBQ0o7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
