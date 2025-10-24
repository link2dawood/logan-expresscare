export default [
    {
        path: "/admin/payrolls/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "pre-payments",
                component: () =>
                    import("../views/payrolls/pre-payments/index.vue"),
                name: "admin.pre_payments.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    menuKey: (route) => "pre_payments",
                    permission: "pre_payments_view",
                },
            },
            {
                path: "increments-promotions",
                component: () =>
                    import("../views/payrolls/increments-promotions/index.vue"),
                name: "admin.increments_promotions.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    menuKey: (route) => "increments_promotions",
                    permission: "increments_promotions_view",
                },
            },
            {
                path: "payrolls",
                component: () => import("../views/payrolls/payroll/index.vue"),
                name: "admin.payrolls.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    menuKey: (route) => "payrolls",
                    permission: "payrolls_view",
                },
            },
            {
                path: "basic-salaries",
                component: () =>
                    import("../views/payrolls/basic-salary/index.vue"),
                name: "admin.basic_salaries.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    menuKey: (route) => "basic_salaries",
                    permission: "salary_settings",
                },
            },
        ],
    },
];
