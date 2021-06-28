import { createRouter, createWebHashHistory } from "vue-router";

import DashboardLayout from "@/layout/DashboardLayout";
import DashboardLayoutAdmin from "@/layout/DashboardLayoutAdmin";
import DashboardLayoutDriver from "@/layout/DashboardLayoutDriver";
import DashboardLayoutCust from "@/layout/DashboardLayoutCustomer";
import AuthLayout from "@/layout/AuthLayout";

import Dashboard from "../views/Dashboard.vue";
import Icons from "../views/Icons.vue";
import Maps from "../views/Maps.vue";
import Profile from "../views/UserProfile.vue";
import Tables from "../views/Tables.vue";

import Login from "../views/Login.vue";
import Register from "../views/Register.vue";


// ADMIN LAYOUT
import AdminDataTable from "../views/admin/AdminDataTable";
import DriverDataTable from "../views/admin/DriverDataTable";
import NewAdminForm from "../views/admin/NewAdminForm";

// DRIVER LAYOUT


// CUSTOMER LAYOUT 

const routes = [
  {
    path: "/",
    redirect: "login",
    component: AuthLayout,
    children: [
      {
        path: "/login",
        name: "login",
        components: { default: Login },
      },
      {
        path: "/register",
        name: "register",
        components: { default: Register },
      },
    ],
  },
  {
    path: "/",
    redirect: "/dashboard",
    component: DashboardLayout,
    children: [
      {
        path: "/dashboard",
        name: "dashboard",
        components: { default: Dashboard },
      },
      {
        path: "/icons",
        name: "icons",
        components: { default: Icons },
      },
      {
        path: "/maps",
        name: "maps",
        components: { default: Maps },
      },
      {
        path: "/profile",
        name: "profile",
        components: { default: Profile },
      },
      {
        path: "/tables",
        name: "tables",
        components: { default: Tables },
      },
    ],
  },
  {
    path: "/admin",
    redirect: "/admin/dashboardAdmin",
    component: DashboardLayoutAdmin,
    children: [
      {
        path: "dashboardAdmin",
        name: "dashboard admin",
        components: { default: Dashboard },
      },
      {
        path: "adminList",
        name: "Data Admin",
        components: { default: AdminDataTable },
      },
      {
        path: "driverList",
        name: "Data Driver",
        components: { default: DriverDataTable },
      },
      {
        path: "newAdmin",
        name: "Create New Admin Account",
        components: { default: NewAdminForm },
      },
      {
        path: "adminList",
        name: "Data Admin",
        components: { default: AdminDataTable },
      },
      {
        path: "/icons",
        name: "icons",
        components: { default: Icons },
      },
      {
        path: "/maps",
        name: "maps",
        components: { default: Maps },
      },
      {
        path: "/profile",
        name: "profile",
        components: { default: Profile },
      },
      {
        path: "/tables",
        name: "tables",
        components: { default: Tables },
      },
    ],
  },
  {
    path: "/driver",
    redirect: "/driver/dashboardDriver",
    component: DashboardLayoutDriver,
    children: [
      {
        path: "dashboardDriver",
        name: "dashboard driver",
        components: { default: Dashboard },
      },
      {
        path: "/icons",
        name: "icons",
        components: { default: Icons },
      },
      {
        path: "/maps",
        name: "maps",
        components: { default: Maps },
      },
      {
        path: "/profile",
        name: "profile",
        components: { default: Profile },
      },
      {
        path: "/tables",
        name: "tables",
        components: { default: Tables },
      },
    ],
  },
  {
    path: "/cust",
    redirect: "/cust/dashboardCust",
    component: DashboardLayoutCust,
    children: [
      {
        path: "dashboardCust",
        name: "dashboard cust",
        components: { default: Dashboard },
      },
      {
        path: "/icons",
        name: "icons",
        components: { default: Icons },
      },
      {
        path: "/maps",
        name: "maps",
        components: { default: Maps },
      },
      {
        path: "/profile",
        name: "profile",
        components: { default: Profile },
      },
      {
        path: "/tables",
        name: "tables",
        components: { default: Tables },
      },
    ],
  },
];

const router = createRouter({
  history: createWebHashHistory(),
  linkActiveClass: "active",
  routes,
});

export default router;
