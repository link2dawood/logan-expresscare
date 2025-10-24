export default [
    {
        path: "/admin/attendances/",
        component: () => import("../../common/layouts/Admin.vue"),
        children: [
            {
                path: "details",
                component:() => import("../views/attendances/details/index.vue"),
                name: "admin.attendance.details",
                meta: {
                    requireAuth: true,
                    menuParent: "attendances",
                    menuKey: (route) => "attendance_details",
                },
            },
            {
                path: "summary",
                component:() => import("../views/attendances/summary/index.vue"),
                name: "admin.attendance.summary",
                meta: {
                    requireAuth: true,
                    menuParent: "attendances",
                    menuKey: (route) => "attendance_summary",
                },
            },
            {
                path: "attendances",
                component:() => import("../views/attendances/attendance/index.vue"),
                name: "admin.attendances.index",
                meta: {
                    requireAuth: true,
                    menuParent: "attendances",
                    menuKey: (route) => "attendances",
                    permission: "attendances_view",
                },
            },
        ],
    },
];
