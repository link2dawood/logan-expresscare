export default [
    {
        path: "/admin/staff-onboarding",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "",
                component: () =>
                    import("../views/staff-onboarding/index.vue"),
                name: "admin.staff_onboarding.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: "staff_onboarding",
                },
            },
            {
                path: ":id",
                component: () =>
                    import("../views/staff-onboarding/View.vue"),
                name: "admin.staff_onboarding.view",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: "staff_onboarding",
                },
            },
        ],
    },
];


