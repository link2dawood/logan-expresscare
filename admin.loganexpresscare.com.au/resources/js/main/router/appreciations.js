export default [
    {
        path: "/admin/appreciations/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "appreciations",
                component: () =>
                    import("../views/appreciation/appreciations/index.vue"),
                name: "admin.appreciations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "appreciations",
                    menuKey: (route) => "appreciations",
                    permission: "appreciations_view",
                },
            },
            {
                path: "awards",
                component: () =>
                    import("../views/appreciation/awards/index.vue"),
                name: "admin.awards.index",
                meta: {
                    requireAuth: true,
                    menuParent: "appreciations",
                    menuKey: (route) => "awards",
                    permission: "awards_view",
                },
            },
        ],
    },
];
