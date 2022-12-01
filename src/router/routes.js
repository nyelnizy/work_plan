const routes = [
  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    children: [
      {
        path: "",
        name: "work",
        component: () => import("pages/SubmitWork.vue"),
      },
      {
        path: "invoice",
        name: "invoice",
        component: () => import("pages/ViewWorks.vue"),
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: "/:catchAll(.*)*",
    component: () => import("pages/Error404.vue"),
  },
];

export default routes;
