export default [
    {
        path: "/admin/letter-heads/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "templates",
                component: () =>
                    import("../views/letter-head/template/index.vue"),
                name: "admin.email-templates.index",
                meta: {
                    requireAuth: true,
                    menuParent: "letter_heads",
                    menuKey: (route) => "templates",
                    permission: "letter_head_templates_view",
                },
            },
            {
                path: "generates",
                component: () =>
                    import("../views/letter-head/generates/index.vue"),
                name: "admin.generates.index",
                meta: {
                    requireAuth: true,
                    menuParent: "letter_heads",
                    menuKey: (route) => "generates",
                    permission: "generates_view",
                },
            },
        ],
    },
];
