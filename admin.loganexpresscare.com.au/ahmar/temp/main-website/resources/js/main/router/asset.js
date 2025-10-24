export default [
    {
        path: "/admin/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "assets",
                component: () => import("../views/assets/asset/index.vue"),
                name: "admin.assets.index",
                meta: {
                    requireAuth: true,
                    menuParent: "assets",
                    menuKey: (route) => "assets",
                    permission: "assets_view",
                },
            },
            {
                path: "asset-types",
                component: () => import("../views/assets/asset-type/index.vue"),
                name: "admin.asset_types.index",
                meta: {
                    requireAuth: true,
                    menuParent: "assets",
                    menuKey: (route) => "asset_types",
                    permission: "asset_types_view",
                },
            },
        ],
    },
];
