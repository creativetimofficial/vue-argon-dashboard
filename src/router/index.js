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
import AdminProfile from "../views/admin/AdminProfile";
import CustomerDataTable from "../views/admin/CustomerDataTable";
import CustomerDetailProfile from "../views/admin/CustomerDetailProfile";
import ActivityDataTable from "../views/admin/ActivityDataTable";
import DriverDetailProfile from "../views/admin/DriverDetailProfile";
import ActivityDetail from "../views/admin/ActivityDetail";
import FeedbackDetail from "../views/admin/FeedbackDetail";
import AdminDashboard from "../views/admin/AdminDashboard";

// DRIVER LAYOUT
import DriverActivityTable from "../views/driver/ActivityTable";
import DriverProfile from "../views/driver/DriverProfile";
import DriverActivityDetail from "../views/driver/DriverActivityDetail";
import DriverFeedbackDetail from "../views/driver/DriverFeedbackDetail";
import NewOrderTable from "../views/driver/NewOrderTable";
import CustomerDetailProfileFromDriver from "../views/driver/CustomerDetail";


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
        name: "Admin Dashboard",
        components: { default: AdminDashboard },
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
        path: "orderList",
        name: "Activity History",
        components: { default: ActivityDataTable },
      },
      {
        path: "newAdmin",
        name: "Create New Admin Account",
        components: { default: NewAdminForm },
      },
      {
        path: "profile",
        name: "Profile",
        components: { default: AdminProfile },
      },
      {
        path: "customerList",
        name: "Data Customer",
        components: { default: CustomerDataTable },
      },
      {
        path: "customerDetail/:id",
        name: "Customer Profile",
        components: { default: CustomerDetailProfile },
      },
      {
        path: "driverDetail/:id",
        name: "Driver Data",
        components: { default: DriverDetailProfile },
      },
      {
        path: "activityDetail/:id",
        name: "Activity Detail",
        components: { default: ActivityDetail },
      },
      {
        path: "feedbackDetail/:id",
        name: "Feedback Detail",
        components: { default: FeedbackDetail },
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
        path: "orderList",
        name: "Driver Activity History",
        components: { default: DriverActivityTable },
      },
      {
        path: "newOrderList",
        name: "New Order",
        components: { default: NewOrderTable },
      },
      {
        path: "profile",
        name: "Driver Profile",
        components: { default: DriverProfile },
      },
      {
        path: "activityDetail/:id",
        name: "Driver Activity Detail",
        components: { default: DriverActivityDetail },
      },
      {
        path: "feedbackDetail/:id",
        name: "Driver Feedback Detail",
        components: { default: DriverFeedbackDetail },
      },
      {
        path: "customerDetail/:id",
        name: "Customer Profile (Driver)",
        components: { default: CustomerDetailProfileFromDriver },
      }
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
    ],
  },
];

const router = createRouter({
  history: createWebHashHistory(),
  linkActiveClass: "active",
  routes,
});

export default router;
