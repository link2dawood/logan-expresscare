export default [
    {
        path: "/admin/self",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "dashboard",
                component: () => import("../views/self/Dashboard.vue"),
                name: "admin.self.dashboard.index",
                meta: {
                    requireAuth: true,
                    menuParent: "dashboard",
                    barKey: "self",
                    menuKey: (route) => "dashboard",
                },
            },
            {
                path: "holidays",
                component: () => import("../views/self/holidays/index.vue"),
                name: "admin.self.holidays.index",
                meta: {
                    requireAuth: true,
                    menuParent: "holidays",
                    barKey: "self",
                    menuKey: (route) => "holidays",
                },
            },
            {
                path: "appreciations",
                component: () =>
                    import("../views/self/appreciations/index.vue"),
                name: "admin.self.appreciations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "appreciations",
                    barKey: "self",
                    menuKey: (route) => "appreciations",
                },
            },
            {
                path: "assets",
                component: () => import("../views/self/asset/index.vue"),
                name: "admin.self.assets.index",
                meta: {
                    requireAuth: true,
                    menuParent: "assets",
                    barKey: "self",
                    menuKey: (route) => "assets",
                    assetType: "asset",
                },
            },
            {
                path: "company-policies",
                component: () =>
                    import("../views/self/company-policies/index.vue"),
                name: "admin.self.company-policies.index",
                meta: {
                    requireAuth: true,
                    menuParent: "company_policies",
                    barKey: "self",
                    menuKey: (route) => "company-policies",
                },
            },
            {
                path: "assigned-survey",
                component: () =>
                    import("../views/self/assigned-survey/index.vue"),
                name: "admin.self.assigned_survey.index",
                meta: {
                    requireAuth: true,
                    menuParent: "forms",
                    barKey: "self",
                    menuKey: (route) => "assigned_survey",
                },
            },
            {
                path: "letter-head",
                component: () => import("../views/self/letter-head/index.vue"),
                name: "admin.self.generates.index",
                meta: {
                    requireAuth: true,
                    menuParent: "letter_heads",
                    barKey: "self",
                    menuKey: (route) => "generates",
                },
            },
            {
                path: "details",
                component: () =>
                    import("../views/self/attendance/details/index.vue"),
                name: "admin.self.attendance.details",
                meta: {
                    requireAuth: true,
                    menuParent: "attendances",
                    barKey: "self",
                    menuKey: (route) => "attendance_details",
                },
            },
            {
                path: "summary",
                component: () =>
                    import("../views/self/attendance/summary/index.vue"),
                name: "admin.self.attendance.summary",
                meta: {
                    requireAuth: true,
                    menuParent: "attendances",
                    barKey: "self",
                    menuKey: (route) => "attendance_summary",
                },
            },
            {
                path: "news",
                component: () => import("../views/self/news/index.vue"),
                name: "admin.self.news.index",
                meta: {
                    requireAuth: true,
                    menuParent: "news",
                    barKey: "self",
                    menuKey: (route) => "news",
                    assetType: "news",
                },
            },
            {
                path: "warnings",
                component: () =>
                    import("../views/self/offboardings/warnings/index.vue"),
                name: "admin.self.warnings.index",
                meta: {
                    requireAuth: true,
                    menuParent: "offboardings",
                    barKey: "self",
                    menuKey: (route) => "warnings",
                    assetType: "warnings",
                },
            },
            {
                path: "resignations",
                component: () =>
                    import("../views/self/offboardings/resignations/index.vue"),
                name: "admin.self.resignations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "offboardings",
                    barKey: "self",
                    menuKey: (route) => "resignations",
                    assetType: "resignations",
                },
            },
            {
                path: "complaints",
                component: () =>
                    import("../views/self/offboardings/complaints/index.vue"),
                name: "admin.self.complaints.index",
                meta: {
                    requireAuth: true,
                    menuParent: "offboardings",
                    barKey: "self",
                    menuKey: (route) => "complaints",
                },
            },
            {
                path: "leaves",
                component: () => import("../views/self/leave/leaves/index.vue"),
                name: "admin.self.leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    barKey: "self",
                    menuKey: (route) => "leaves",
                },
            },
            {
                path: "remaining-leaves",
                component: () =>
                    import("../views/self/leave/remaining-leaves/index.vue"),
                name: "admin.self.remaining-leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    barKey: "self",
                    menuKey: (route) => "remaining_leaves",
                },
            },
            {
                path: "unpaid-leaves",
                component: () =>
                    import("../views/self/leave/unpaid-leaves/index.vue"),
                name: "admin.self.unpaid-leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    barKey: "self",
                    menuKey: (route) => "unpaid_leaves",
                },
            },
            {
                path: "paid-leaves",
                component: () =>
                    import("../views/self/leave/paid-leaves/index.vue"),
                name: "admin.self.paid-leaves.index",
                meta: {
                    requireAuth: true,
                    menuParent: "leaves",
                    barKey: "self",
                    menuKey: (route) => "paid_leaves",
                },
            },
            {
                path: "pre_payments",
                component: () =>
                    import("../views/self/payrolls/pre-payments/index.vue"),
                name: "admin.self.pre_payments.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    barKey: "self",
                    menuKey: (route) => "pre_payments",
                },
            },
            {
                path: "increments_payments",
                component: () =>
                    import(
                        "../views/self/payrolls/increments-promotions/index.vue"
                    ),
                name: "admin.self.increments_payments.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    barKey: "self",
                    menuKey: (route) => "increments_promotions",
                },
            },
            {
                path: "payrolls",
                component: () =>
                    import("../views/self/payrolls/payroll/index.vue"),
                name: "admin.self.payrolls.index",
                meta: {
                    requireAuth: true,
                    menuParent: "payrolls",
                    barKey: "self",
                    menuKey: (route) => "payrolls",
                },
            },
            {
                path: "custom-data-fields",
                component: () =>
                    import("../views/self/custom-data-fields/index.vue"),
                name: "admin.self.custom-data-fields.index",
                meta: {
                    requireAuth: true,
                    menuParent: "custom_data_fields",
                    barKey: "self",
                    menuKey: (route) => "custom_data_fields",
                },
            },
            {
                path: "profile",
                component: () => import("../views/self/profile/Edit.vue"),
                name: "admin.self.profile.index",
                meta: {
                    requireAuth: true,
                    menuParent: "profile",
                    barKey: "self",
                    menuKey: (route) => "profile",
                },
            },
            {
                path: "expenses",
                component: () => import("../views/self/expenses/index.vue"),
                name: "admin.self.expenses.index",
                meta: {
                    requireAuth: true,
                    menuParent: "expenses",
                    barKey: "self",
                    menuKey: (route) => "expenses",
                },
            },
        ],
    },
];
