export default [
    {
        path: "/admin/users",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "/admin/staffs",
                component: () =>
                    import("../views/staff-members/users/index.vue"),
                name: "admin.staffs.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: "staff",
                    permission: "users_view",
                },
            },
            {
                path: "departments",
                component: () =>
                    import("../views/staff-members/departments/index.vue"),
                name: "admin.departments.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: (route) => "departments",
                    permission: "departments_view",
                },
            },
            {
                path: "designations",
                component: () =>
                    import("../views/staff-members/designations/index.vue"),
                name: "admin.designations.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: (route) => "designations",
                    permission: "designations_view",
                },
            },
            {
                path: "shifts",
                component: () =>
                    import("../views/staff-members/shifts/index.vue"),
                name: "admin.shifts.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: (route) => "shifts",
                    permission: "shifts_view",
                },
            },
            {
                path: "employees/:id",
                component: () =>
                    import("../views/staff-members/users/index.vue"),
                name: "admin.employees.index",
                meta: {
                    requireAuth: true,
                    menuParent: "staff",
                    menuKey: (route) => "employees",
                    permission: "employees_view",
                },
            },
        ],
    },
];
