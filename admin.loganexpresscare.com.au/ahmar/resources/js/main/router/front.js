export default [
    {
        path: "/",
        component: () => import("../views/front/layouts/FrontLayout.vue"),
        children: [
            {
                path: "/job-details/:id",
                component: () => import("../views/front/layouts/JobDetails.vue"),
                name: "front.job.details",
                meta: {
                    requireUnauth: true,
                    menuKey: "application",
                },
            },
            {
                path: "/carrers/:id",
                component: () => import("../views/front/layouts/ApplicationForm.vue"),
                name: "front.career.application",
                meta: {
                    requireUnauth: true,
                    menuKey: "application",
                },
            },
            {
                path: "/carrers",
                component: () => import("../views/front/Career.vue"),
                name: "front.career",
                meta: {
                    requireUnauth: true,
                    menuKey: "homepage",
                },
            },
         
        ],
    },
];
