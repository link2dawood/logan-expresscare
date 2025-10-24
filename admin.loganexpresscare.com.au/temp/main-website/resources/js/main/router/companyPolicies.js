export default [
    {
        path: "/admin/company-policies/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "company-policies",
                component: () => import("../views/company-policies/index.vue"),
                name: "admin.company-policies.index",
                meta: {
                    requireAuth: true,
                    menuParent: "company_policies",
                    menuKey: (route) => "company-policies",
                },
            },
        ],
    },
];
